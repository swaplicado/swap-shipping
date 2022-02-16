<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Trailer;
use App\Models\Sat\TrailerSubtype;
use App\Models\Carrier;
use Validator;

class TrailerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        if(auth()->user()->isCarrier()){
            $data = Trailer::where('carrier_id', auth()->user()->carrier()->first()->id_carrier)->get();
        } else if(auth()->user()->isAdmin() || auth()->user()->isClient()) {
            $data = Trailer::get();
        }
        $data->each( function ($data) {
            $data->TrailerSubtype;
            $data->Carrier;
        });

        $carriers = Carrier::where('is_deleted', 0)->select('id_carrier','fullname')->get();

        return view('ship/trailers/index', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        $data = new Trailer;
        $data->TrailerSubtype = new TrailerSubtype;
        $data->Carrier = new Carrier;
        $TrailerSubtype = TrailerSubtype::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');

        return view('ship/trailers/create', ['data' => $data, 'TrailerSubtype' => $TrailerSubtype, 'carriers' => $carriers]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        if(auth()->user()->isCarrier()){
            $request->request->add(['carrier' => auth()->user()->carrier()->first()->id_carrier]);
        }
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'plates' => 'required',
            'carrier' => 'required|not_in:0',
            'trailer_subtype_id' => 'required'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        
        try {
            DB::transaction(function () use ($request, $user_id) {
                $trailer = Trailer::create([
                    'plates' => $request->plates,
                    'trailer_subtype_id' => $request->trailer_subtype_id,
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

        return redirect('trailers')->with(['mesage' => $msg, 'icon' => $icon]);
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
        auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        $data = Trailer::where('id_trailer', $id)->first();
        auth()->user()->carrierAutorization($data->carrier_id);
        $data->each(function ($data) {
            $data->TrailerSubtype;
            $data->Carrier;
        });
        $TrailerSubtype = TrailerSubtype::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');

        return view('ship/trailers/edit', ['data' => $data, 'TrailerSubtype' => $TrailerSubtype]);
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
        auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'plates' => 'required',
            'trailer_subtype_id' => 'required'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        try {
            DB::transaction(function () use ($request, $user_id, $id) {
                $trailer = Trailer::findOrFail($id);
                auth()->user()->carrierAutorization($trailer->carrier_id);
                $trailer->plates = $request->plates;
                $trailer->trailer_subtype_id = $request->trailer_subtype_id;
                // $trailer->carrier_id = $request->carrier_id;
                $trailer->usr_upd_id = $user_id;

                $trailer->update();
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

        return redirect('trailers')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $trailer = Trailer::findOrFail($id);
                auth()->user()->carrierAutorization($trailer->carrier_id);
                $trailer->is_deleted = 1;
                $trailer->usr_upd_id = $user_id;

                $trailer->update();
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

        return redirect('trailers')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $trailer = Trailer::findOrFail($id);
                auth()->user()->carrierAutorization($trailer->carrier_id);
                $trailer->is_deleted = 0;
                $trailer->usr_upd_id = $user_id;

                $trailer->update();
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

        return redirect('trailers')->with(['mesage' => $msg, 'icon' => $icon]);
    }
}
