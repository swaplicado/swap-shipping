<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;
use App\Models\Sat\LicenceSct;
use App\Models\Sat\VehicleConfig;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
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
        $data = Vehicle::get();
        $data->each(function ($data) {
            $data->LicenceSct;
            $data->VehicleConfig;
            $data->Carrier;
        });

        return view('ship/vehicles/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = new Vehicle;
        $data->LicenceSct = new LicenceSct;
        $data->VehicleConfig = new VehicleConfig;
        $data->Carrier = new Carrier;
        $LicenceSct = LicenceSct::pluck('id', 'key_code');
        $VehicleConfig = VehicleConfig::pluck('id', 'key_code');
        $Carrier = Carrier::pluck('id_carrier', 'fullname');

        return view('ship/vehicles/create', ['data' => $data, 'LicenceSct' => $LicenceSct, 
            'VehicleConfig' => $VehicleConfig, 'Carrier' => $Carrier]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'plates' => 'required',
            'license_sct_num' => 'required',
            'license_sct_id' => 'required',
            'veh_cfg_id' => 'required',
            'carrier_id' => 'required'
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
                    'carrier_id' => $request->carrier_id,
                    'usr_new_id' => $user_id,
                    'usr_upd_id' => $user_id
                ]);
            });
        } catch (QueryException $e) {
            $success = false;
            $error = $e->errorInfo[0];
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
        $data = Vehicle::where('id_vehicle', $id)->get();
        $data->each(function ($data) {
            $data->LicenceSct;
            $data->VehicleConfig;
            $data->Carrier;
        });
        $LicenceSct = LicenceSct::pluck('id', 'key_code');
        $VehicleConfig = VehicleConfig::pluck('id', 'key_code');
        $Carrier = Carrier::pluck('id_carrier', 'fullname');

        return view('ship/vehicles/edit', ['data' => $data, 'LicenceSct' => $LicenceSct, 
            'VehicleConfig' => $VehicleConfig, 'Carrier' => $Carrier ]);
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
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'plates' => 'required',
            'license_sct_num' => 'required',
            'license_sct_id' => 'required',
            'veh_cfg_id' => 'required',
            'carrier_id' => 'required'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        try {
            DB::transaction(function () use ($request, $user_id, $id) {
                $Vehicle = Vehicle::findOrFail($id);

                $Vehicle->plates = $request->plates;
                $Vehicle->year_model = $request->year_model;
                $Vehicle->license_sct_num = $request->license_sct_num;
                $Vehicle->drvr_reg_trib = $request->drvr_reg_trib;
                $Vehicle->policy = $request->policy;
                $Vehicle->license_sct_id = $request->license_sct_id;
                $Vehicle->veh_cfg_id = $request->veh_cfg_id;
                $Vehicle->carrier_id = $request->carrier_id;
                $Vehicle->usr_upd_id = $user_id;

                $Vehicle->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = $e->errorInfo[0];
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
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $Vehicle = Vehicle::findOrFail($id);

                $Vehicle->is_deleted = 1;
                $Vehicle->usr_upd_id = $user_id;

                $Vehicle->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = $e->errorInfo[0];
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

    public function recover($id){
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $Vehicle = Vehicle::findOrFail($id);

                $Vehicle->is_deleted = 0;
                $Vehicle->usr_upd_id = $user_id;

                $Vehicle->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = $e->errorInfo[0];
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
