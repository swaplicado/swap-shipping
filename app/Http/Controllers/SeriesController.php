<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Series;
use App\Models\Carrier;
use Illuminate\Database\QueryException;
use Validator;
use Auth;

class SeriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->isCarrier()){
            $data = Series::where('carrier_id', auth()->user()->carrier()->first()->id_carrier)->get();
        } else if (auth()->user()->isAdmin()){
            $data = Series::get();
        }

        $carriers = Carrier::where('is_deleted', 0)->select('id_carrier','fullname')->get();

        return view('catalogos/series/index', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = new Series;
        $data->Carrier = new Carrier;
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');

        return view('catalogos/series/create', ['data' => $data]);
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
            'serie_name' => 'required',
            'prefix' => 'required',
            'initial_number' => 'required',
            'description' => 'required'
        ]);

        $validator->validate();
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($request, $user_id) {
                $serie = Series::create([
                    'serie_name' => $request->serie_name,
                    'prefix' => $request->prefix,
                    'initial_number' => $request->initial_number,
                    'description' => $request->description,
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

        return redirect('series')->with(['mesage' => $msg, 'icon' => $icon]);
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
        $data = Series::where('id_serie', $id)->first();
        auth()->user()->carrierAutorization($data->carrier_id);
        $data->Carrier = new Carrier;

        return view('catalogos/series/edit', ['data' => $data]);
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
            'serie_name' => 'required',
            'prefix' => 'required',
            'initial_number' => 'required',
            'description' => 'required'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        try {
            DB::transaction(function () use ($request, $user_id, $id) {
                $Serie = Series::findOrFail($id);
                auth()->user()->carrierAutorization($Serie->carrier_id);
                $Serie->serie_name = $request->serie_name;
                $Serie->prefix = $request->prefix;
                $Serie->initial_number = $request->initial_number;
                $Serie->description = $request->description;
                $Serie->usr_upd_id = $user_id;

                $Serie->update();
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

        return redirect('series')->with(['mesage' => $msg, 'icon' => $icon]);
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
                $Serie = Series::findOrFail($id);
                auth()->user()->carrierAutorization($Serie->carrier_id);
                $Serie->is_deleted = 1;
                $Serie->usr_upd_id = $user_id;

                $Serie->update();
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

        return redirect('series')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $Serie = Series::findOrFail($id);
                auth()->user()->carrierAutorization($Serie->carrier_id);
                $Serie->is_deleted = 0;
                $Serie->usr_upd_id = $user_id;

                $Serie->update();
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

        return redirect('series')->with(['mesage' => $msg, 'icon' => $icon]);
    }
}
