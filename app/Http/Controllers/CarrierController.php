<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Validator;
use Auth;
use App\User;
use App\Role;
use App\UserPivot;
use Illuminate\Support\Facades\Hash;

class CarrierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // auth()->user()->authorizeRoles(['user', 'admin']);
        // auth()->user()->authorizePermission(['A1','A2','A3']);
        $data = Carrier::get();
        return view('ship/carriers/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // auth()->user()->authorizeRoles(['user', 'admin']);
        $data = new Carrier;
        return view('ship/carriers/create', ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // auth()->user()->authorizeRoles(['user', 'admin']);
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'fullname' => 'required',
            'RFC' => 'required'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        
        try {
            DB::transaction(function () use ($request, $user_id) {
                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'full_name' => $request->fullname,
                    'user_type_id' => 3,
                    'is_carrier' => 1
                ]);
        
                $user->roles()->attach(Role::where('id', 3)->first());

                $carrier = Carrier::create([
                    'fullname' => $request->fullname,
                    'fiscal_id' => $request->RFC,
                    'usr_new_id' => $user_id,
                    'usr_upd_id' => $user_id
                ]);
                
                $UserPivot = UserPivot::create([
                    'carrier_id' => $carrier->id_carrier,
                    'user_id' => $user->id
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

        return redirect('carriers')->with(['mesage' => $msg, 'icon' => $icon]);
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
        // auth()->user()->authorizeRoles(['user', 'admin']);
        $data = Carrier::where('id_carrier', $id)->get();
        $data->each(function ($data) {
            $data->User;
        });
        return view('ship/carriers/edit', ['data' => $data]);
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
        // auth()->user()->authorizeRoles(['user', 'admin']);
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'fullname' => 'required',
            'email' => 'required',
            'RFC' => 'required'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        try {
            DB::transaction(function () use ($request, $user_id, $id) {
                $carrier = Carrier::findOrFail($id);
                $user = User::findOrFail($carrier->usr_id);

                $carrier->fullname = $request->fullname;
                $carrier->fiscal_id = $request->RFC;
                $carrier->usr_upd_id = $user_id;

                $user->username = $request->username;
                $user->full_name = $request->fullname;
                $user->email = $request->email;

                $carrier->update();
                $user->update();
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

        return redirect('carriers')->with(['mesage' => $msg, 'icon' => $icon]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Carrier  $carrier
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $carrier = Carrier::findOrFail($id);
                $user = User::findOrFail($carrier->usr_id);

                $carrier->is_deleted = 1;
                $carrier->usr_upd_id = $user_id;

                $user->is_deleted = 1;

                $userPivot = UserPivot::where('carrier_id', $carrier->id_carrier)->firstOrFail();
                $userPivot->is_deleted = 1;

                $carrier->update();
                $user->update();
                $userPivot->update();
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

        return redirect('carriers')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    public function recover($id) 
    {
        // auth()->user()->authorizeRoles(['user', 'admin']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $carrier = Carrier::findOrFail($id);
                $user = User::findOrFail($carrier->usr_id);

                $carrier->is_deleted = 0;
                $carrier->usr_upd_id = $user_id;

                $user->is_deleted = 0;

                $userPivot = UserPivot::where('carrier_id', $carrier->id_carrier)->firstOrFail();
                $userPivot->is_deleted = 0;

                $carrier->update();
                $user->update();
                $userPivot->update();
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

        return redirect('carriers')->with(['mesage' => $msg, 'icon' => $icon]);
    }
}
