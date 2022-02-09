<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Insurances;
use App\Models\Carrier;
use Validator;
use Auth;

class InsurancesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->isCarrier()){
            $data = Insurances::where('carrier_id', auth()->user()->carrier()->first()->id_carrier)->get();
        } else if (auth()->user()->isAdmin()){
            $data = Insurances::get();    
        }

        $data->each(function ($data) {
            $data->Carrier;
        });
        return view('catalogos/insurances/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = new Insurances;
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');
        return view('catalogos/insurances/create', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(auth()->user()->isCarrier()){
            $request->request->add(['carrier' => auth()->user()->carrier()->first()->id_carrier]);
        }
        $success = true;
        $error = "0";
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'carrier' => 'required|not_in:0',
            'checkbox' => 'required'
        ]);

        $validator->validate();

        $resp_civ = 0;
        $ambiental = 0;
        $carga = 0;
        foreach($request->checkbox as $c){
            switch($c){
                case "1":
                    $resp_civ = 1;
                    break;
                case "2":
                    $ambiental = 1;
                    break;
                case "3":
                    $carga = 1;
                    break;
                default:
                    break;
            }
        }

        $user_id = (auth()->check()) ? auth()->user()->id : null;
        
        try {
            DB::transaction(function () use ($request, $user_id, $resp_civ, $ambiental, $carga) {
                $Insurance = Insurances::create([
                    'full_name' => $request->fullname,
                    'is_civ_resp' => $resp_civ,
                    'is_ambiental' => $ambiental,
                    'is_cargo' => $carga,
                    'carrier_id' => $request->carrier,
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

        return redirect('insurances')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = Insurances::where('id_insurance', $id)->first();
        auth()->user()->carrierAutorization($data->carrier_id);
        return view('catalogos/insurances/edit', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $success = true;
        $error = "0";
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'checkbox' => 'required'
        ]);

        $validator->validate();

        $resp_civ = 0;
        $ambiental = 0;
        $carga = 0;
        foreach($request->checkbox as $c){
            switch($c){
                case "1":
                    $resp_civ = 1;
                    break;
                case "2":
                    $ambiental = 1;
                    break;
                case "3":
                    $carga = 1;
                    break;
                default:
                    break;
            }
        }

        $user_id = (auth()->check()) ? auth()->user()->id : null;
        
        try {
            DB::transaction(function () use ($request, $user_id, $resp_civ, $ambiental, $carga, $id) {
                $Insurance = Insurances::findOrFail($id);
                auth()->user()->carrierAutorization($Insurance->carrier_id);
                $Insurance->full_name = $request->fullname;
                $Insurance->is_civ_resp = $resp_civ;
                $Insurance->is_ambiental = $ambiental;
                $Insurance->is_cargo = $carga;
                $Insurance->usr_upd_id = $user_id;
                
                $Insurance->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = $e->errorInfo[0];
        }

        if ($success) {
            $msg = "Se actualizó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al actualizar el registro Error: " . $error;
            $icon = "error";
        }

        return redirect('insurances')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $Insurance = Insurances::findOrFail($id);
                auth()->user()->carrierAutorization($Insurance->carrier_id);
                $Insurance->is_deleted = 1;
                $Insurance->usr_upd_id = $user_id;

                $Insurance->update();
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

        return redirect('insurances')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $Insurance = Insurances::findOrFail($id);
                auth()->user()->carrierAutorization($Insurance->carrier_id);
                $Insurance->is_deleted = 0;
                $Insurance->usr_upd_id = $user_id;

                $Insurance->update();
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

        return redirect('insurances')->with(['mesage' => $msg, 'icon' => $icon]);
    }
}
