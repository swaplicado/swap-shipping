<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\FigureT;
use App\Models\Insurances;
use App\Models\Sat\LicenceSct;
use App\Models\Sat\VehicleConfig;
use App\Models\TransFigCfg;
use App\Models\TransportPart;
use App\Models\Vehicle;
use App\Models\VehicleKey;
use App\Utils\messagesErros;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class VehicleController extends Controller
{
    private $attributeNames = array(
        'plates' => 'Placas',
        'license_sct_num' => 'Número de permiso SCT',
        'license_sct_id' => 'Permiso SCT',
        'veh_cfg_id' => 'Configuración del vehículo',
        'veh_key_id' => 'Clave del vehículo',
        'insurance' => 'Aseguradora',
        'carrier' => 'Transportista'
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['221']);
        if(auth()->user()->isCarrier()){
            $data = Vehicle::where('carrier_id', auth()->user()->carrier()->first()->id_carrier)->get();
        } else if (auth()->user()->isAdmin() || auth()->user()->isClient()){
            $data = Vehicle::get();
        }
        
        $data->each(function ($data) {
            $data->LicenceSct;
            $data->VehicleConfig;
            $data->VehicleKey;
            $data->Insurance;
        });

        $carriers = Carrier::where('is_deleted', 0)->select('id_carrier','fullname')->get();

        return view('ship/vehicles/index', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['222']);
        $data = new Vehicle;
        $data->is_own = true;
        $data->LicenceSct = new LicenceSct;
        $data->VehicleConfig = new VehicleConfig;
        $data->Carrier = new Carrier;
        
        $LicenceSct = LicenceSct::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $VehicleConfig = VehicleConfig::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $lVehicleKeys = VehicleKey::get();

        $lFigures = FigureT::select('id_trans_figure',
                                    'fullname',
                                    'fiscal_id',
                                    'carrier_id'
                                )
                            ->where('is_deleted', false);

        if (auth()->user()->isCarrier()) {
            $idCarrier = auth()->user()->carrier()->first()->id_carrier;
            $Insurances = Insurances::where('carrier_id', $idCarrier)->pluck('id_insurance', 'full_name');
            $lFigures = $lFigures->where('carrier_id', $idCarrier);
        }
        else if (auth()->user()->isAdmin() || auth()->user()->isClient()) {
            // $Insurances = Insurances::pluck('id_insurance', 'full_name');
            $Insurances = Insurances::get();
        }
        
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');
        $lFigures = $lFigures->get();

        $lTransParts = TransportPart::select('id', 'key_code', 'description')
                                    ->whereIn('id', [1, 2, 3, 6])
                                    ->get();

        return view('ship/vehicles/create', [
                        'data' => $data, 
                        'LicenceSct' => $LicenceSct, 
                        'VehicleConfig' => $VehicleConfig, 
                        'insurances' => $Insurances, 
                        'carriers' => $carriers,
                        'lFigures' => $lFigures,
                        'lTransParts' => $lTransParts,
                        'lVehicleKeys' => $lVehicleKeys
                    ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['222']);
        if(auth()->user()->isCarrier()){
            $request->request->add(['carrier' => auth()->user()->carrier()->first()->id_carrier]);
        }
        $success = true;
        $error = "0";
        
        $validator = Validator::make($request->all(), [
            'plates' => 'required',
            'license_sct_num' => 'required',
            'license_sct_id' => 'required|not_in:0',
            'veh_cfg_id' => 'required|not_in:0',
            'veh_key_id' => 'required|not_in:0',
            'carrier' => 'required|not_in:0',
            'insurance' => 'required|not_in:0'
        ]);
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::beginTransaction();
            
            $vehicle = Vehicle::create([
                'alias' => $request->alias,                    
                'plates' => mb_strtoupper($request->plates, 'UTF-8'),
                'year_model' => $request->year_model,
                'license_sct_num' => mb_strtoupper($request->license_sct_num, 'UTF-8'),
                'policy' => mb_strtoupper($request->policy, 'UTF-8'),
                'is_own' => isset($request->is_own),
                'license_sct_id' => $request->license_sct_id,
                'veh_cfg_id' => $request->veh_cfg_id,
                'veh_key_id' => $request->veh_key_id,
                'trans_part_n_id' => null,
                'carrier_id' => $request->carrier,
                'insurance_id' => $request->insurance,
                'usr_new_id' => $user_id,
                'usr_upd_id' => $user_id
            ]);

            if (! $vehicle->is_own) {
                $vehicle->trans_part_n_id = $request->trans_part_id;
                $vehicle->save();

                $oTrFigCfg = new TransFigCfg();

                $oTrFigCfg->trans_part_id = $request->trans_part_id;
                $oTrFigCfg->veh_tra_id = $vehicle->id_vehicle;
                $oTrFigCfg->figure_type_id = $request->figure_type;
                $oTrFigCfg->figure_trans_id = $request->figure_id;

                $oTrFigCfg->save();
            }

            DB::commit();
        }
        catch (QueryException $e) {
            DB::rollBack();
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se guardó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al guardar el registro Error: " . $error;
            $icon = "error";
        }

        return redirect('vehicles')->with(['message' => $msg, 'icon' => $icon]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Carrier $carrier
     * 
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['223']);
        $data = Vehicle::where('id_vehicle', $id)->first();
        auth()->user()->carrierAutorization($data->carrier_id);
        $data->each(function ($data) {
            $data->LicenceSct;
            $data->VehicleConfig;
            $data->Carrier;
        });
        
        $LicenceSct = LicenceSct::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $VehicleConfig = VehicleConfig::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $lVehicleKeys = VehicleKey::get();
        $Insurances = Insurances::where('carrier_id', $data->carrier_id)->pluck('id_insurance', 'full_name');

        $lTransParts = TransportPart::select('id', 'key_code', 'description')
                                        ->whereIn('id', [1, 2, 3, 6])
                                        ->get();

        $lFigures = FigureT::select('id_trans_figure',
                                    'fullname',
                                    'fiscal_id',
                                    'carrier_id'
                                )
                            ->where('carrier_id', $data->carrier_id)
                            ->where('is_deleted', false)
                            ->get();

        $oTransCfg = TransFigCfg::where('veh_tra_id', $id)->first();
        
        return view('ship/vehicles/edit', ['data' => $data,
                                            'oTransCfg' => $oTransCfg,
                                            'LicenceSct' => $LicenceSct,
                                            'VehicleConfig' => $VehicleConfig,
                                            'insurances' => $Insurances,
                                            'lTransParts' => $lTransParts,
                                            'lFigures' => $lFigures,
                                            'lVehicleKeys' => $lVehicleKeys ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Carrier $carrier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['223']);
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'plates' => 'required',
            'license_sct_num' => 'required',
            'license_sct_id' => 'required',
            'veh_cfg_id' => 'required',
            'veh_key_id' => 'required'
        ]);
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        try {
            DB::transaction(function () use ($request, $user_id, $id) {
                $Vehicle = Vehicle::findOrFail($id);
                auth()->user()->carrierAutorization($Vehicle->carrier_id);
                $Vehicle->alias = $request->alias;
                $Vehicle->plates = mb_strtoupper($request->plates, 'UTF-8');
                $Vehicle->year_model = $request->year_model;
                $Vehicle->license_sct_num = mb_strtoupper($request->license_sct_num, 'UTF-8');
                $Vehicle->policy = mb_strtoupper($request->policy, 'UTF-8');
                $Vehicle->is_own = isset($request->is_own);
                $Vehicle->license_sct_id = $request->license_sct_id;
                $Vehicle->veh_cfg_id = $request->veh_cfg_id;
                $Vehicle->veh_key_id = $request->veh_key_id;
                $Vehicle->trans_part_n_id = $Vehicle->is_own ? null : $request->trans_part_id;
                $Vehicle->usr_upd_id = $user_id;

                $Vehicle->update();

                $oTransCfg = TransFigCfg::where('veh_tra_id', $id)->first();

                $oTransCfg->trans_part_id = $request->trans_part_id;
                $oTransCfg->figure_type_id = $request->figure_type;
                $oTransCfg->figure_trans_id = $request->figure_id;

                $oTransCfg->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se actualizó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al actualizar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('vehicles')->with(['message' => $msg, 'icon' => $icon]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Carrier $carrier
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['224']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $Vehicle = Vehicle::findOrFail($id);
                auth()->user()->carrierAutorization($Vehicle->carrier_id);
                $Vehicle->is_deleted = 1;
                $Vehicle->usr_upd_id = $user_id;

                $Vehicle->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se eliminó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al eliminar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('vehicles')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['225']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $Vehicle = Vehicle::findOrFail($id);
                auth()->user()->carrierAutorization($Vehicle->carrier_id);
                $Vehicle->is_deleted = 0;
                $Vehicle->usr_upd_id = $user_id;

                $Vehicle->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se recuperó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al recuperar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('vehicles')->with(['message' => $msg, 'icon' => $icon]);
    }
}
