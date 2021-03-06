<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Models\Insurances;
use App\Models\Carrier;
use Validator;
use Auth;
use App\Utils\messagesErros;

class InsurancesController extends Controller
{
    private $attributeNames = array(
        'fullname' => 'Nombre',
        'carrier' => 'Transportista'
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth()->user()->authorizePermission(['421']);
        if(auth()->user()->isCarrier()){
            $data = Insurances::where('carrier_id', auth()->user()->carrier()->first()->id_carrier)->get();
        } else if (auth()->user()->isAdmin() || auth()->user()->isClient()){
            $data = Insurances::get();    
        }

        $data->each(function ($data) {
            $data->Carrier;
        });

        $carriers = Carrier::where('is_deleted', 0)->select('id_carrier','fullname')->get();

        return view('catalogos/insurances/index', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        auth()->user()->authorizePermission(['422']);
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
        auth()->user()->authorizePermission(['422']);
        if(auth()->user()->isCarrier()){
            $request->request->add(['carrier' => auth()->user()->carrier()->first()->id_carrier]);
        }
        $success = true;
        $error = "0";
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'carrier' => 'required|not_in:0'
        ]);
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        $resp_civ = 0;
        $ambiental = 0;
        $carga = 0;

        $user_id = (auth()->check()) ? auth()->user()->id : null;
        
        try {
            DB::transaction(function () use ($request, $user_id, $resp_civ, $ambiental, $carga) {
                $Insurance = Insurances::create([
                    'full_name' => mb_strtoupper($request->fullname, 'UTF-8'),
                    'carrier_id' => $request->carrier,
                    'usr_new_id' => $user_id,
                    'usr_upd_id' => $user_id
                ]);
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se guard?? el registro con ??xito";
            $icon = "success";
        } else {
            $msg = "Error al guardar el registro Error: " . $error;
            $icon = "error";
        }

        return redirect('insurances')->with(['message' => $msg, 'icon' => $icon]);
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
        auth()->user()->authorizePermission(['423']);
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
        auth()->user()->authorizePermission(['423']);
        $success = true;
        $error = "0";
        $validator = Validator::make($request->all(), [
            'fullname' => 'required'
        ]);
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        $resp_civ = 0;
        $ambiental = 0;
        $carga = 0;

        $user_id = (auth()->check()) ? auth()->user()->id : null;
        
        try {
            DB::transaction(function () use ($request, $user_id, $resp_civ, $ambiental, $carga, $id) {
                $Insurance = Insurances::findOrFail($id);
                auth()->user()->carrierAutorization($Insurance->carrier_id);
                $Insurance->full_name = mb_strtoupper($request->fullname);
                $Insurance->usr_upd_id = $user_id;
                
                $Insurance->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se actualiz?? el registro con ??xito";
            $icon = "success";
        } else {
            $msg = "Error al actualizar el registro Error: " . $error;
            $icon = "error";
        }

        return redirect('insurances')->with(['message' => $msg, 'icon' => $icon]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        auth()->user()->authorizePermission(['424']);
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
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se elimin?? el registro con ??xito";
            $icon = "success";
        } else {
            $msg = "Error al eliminar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('insurances')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        auth()->user()->authorizePermission(['425']);
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
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se recuper?? el registro con ??xito";
            $icon = "success";
        } else {
            $msg = "Error al recuperar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('insurances')->with(['message' => $msg, 'icon' => $icon]);
    }
}
