<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;
use App\Models\Insurances;
use App\Models\Sat\LicenceSct;
use App\Models\Sat\VehicleConfig;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Utils\messagesErros;
use Validator;
use Auth;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
            $data->Insurance;
        });

        $carriers = Carrier::where('is_deleted', 0)->select('id_carrier','fullname')->get();

        return view('ship/vehicles/index', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['222']);
        $data = new Vehicle;
        $data->LicenceSct = new LicenceSct;
        $data->VehicleConfig = new VehicleConfig;
        $data->Carrier = new Carrier;
        
        $LicenceSct = LicenceSct::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $VehicleConfig = VehicleConfig::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        if(auth()->user()->isCarrier()){
            $Insurances = Insurances::where('carrier_id', auth()->user()->carrier()->first()->id_carrier)->pluck('id_insurance', 'full_name');
        } else if (auth()->user()->isAdmin() || auth()->user()->isClient()){
            $Insurances = Insurances::pluck('id_insurance', 'full_name');
        }
        
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');

        $Insurances = Insurances::get();

        return view('ship/vehicles/create', ['data' => $data, 'LicenceSct' => $LicenceSct, 
            'VehicleConfig' => $VehicleConfig, 'insurances' => $Insurances, 'carriers' => $carriers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
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
            'carrier' => 'required|not_in:0',
            'insurance' => 'required|not_in:0'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($request, $user_id) {
                $vehicle = Vehicle::create([
                    'plates' => $request->plates,
                    'year_model' => $request->year_model,
                    'license_sct_num' => $request->license_sct_num,
                    'drvr_reg_trib' => $request->drvr_reg_trib,
                    'policy' => $request->policy,
                    'license_sct_id' => $request->license_sct_id,
                    'veh_cfg_id' => $request->veh_cfg_id,
                    'carrier_id' => $request->carrier,
                    'insurance_id' => $request->insurance,
                    'usr_new_id' => $user_id,
                    'usr_upd_id' => $user_id
                ]);
            });
        } catch (QueryException $e) {
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

        return redirect('vehicles')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Carrier  $carrier
     * @return \Illuminate\Http\Response
     */
    public function show(Carrier $carrier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Carrier  $carrier
     * @return \Illuminate\Http\Response
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
        $Insurances = Insurances::where('carrier_id', $data->carrier_id)->pluck('id_insurance', 'full_name');
        
        return view('ship/vehicles/edit', ['data' => $data, 'LicenceSct' => $LicenceSct, 
            'VehicleConfig' => $VehicleConfig, 'insurances' => $Insurances ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Carrier  $carrier
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
            'veh_cfg_id' => 'required'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        try {
            DB::transaction(function () use ($request, $user_id, $id) {
                $Vehicle = Vehicle::findOrFail($id);
                auth()->user()->carrierAutorization($Vehicle->carrier_id);
                $Vehicle->plates = $request->plates;
                $Vehicle->year_model = $request->year_model;
                $Vehicle->license_sct_num = $request->license_sct_num;
                $Vehicle->drvr_reg_trib = $request->drvr_reg_trib;
                $Vehicle->policy = $request->policy;
                $Vehicle->license_sct_id = $request->license_sct_id;
                $Vehicle->veh_cfg_id = $request->veh_cfg_id;
                $Vehicle->usr_upd_id = $user_id;

                $Vehicle->update();
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

        return redirect('vehicles')->with(['mesage' => $msg, 'icon' => $icon]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Carrier  $carrier
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
                auth()->user()->carrierAutorization($vehicle->carrier_id);
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

        return redirect('vehicles')->with(['mesage' => $msg, 'icon' => $icon]);
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
                auth()->user()->carrierAutorization($vehicle->carrier_id);
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

        return redirect('vehicles')->with(['mesage' => $msg, 'icon' => $icon]);
    }
}
