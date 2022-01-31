<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\RoleUser;
use App\Models\Carrier;
use App\Models\TpFigure;
use Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // auth()->user()->authorizeRoles(['admin']);
        $data = User::get();
        $data->each(function ($data) {
            $data->getRoles;
        });
        return view('sys/users/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        // auth()->user()->authorizeRoles(['admin']);
        $data = User::where('id', $id)->get();
        $data->each(function ($data) {
            $data->getRoles;
        });

        $roles = Role::where('is_deleted', '!=', 1)->get();

        return view('sys/users/edit', ['data' => $data, 'roles' => $roles]);
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
        // auth()->user()->authorizeRoles(['admin']);
        $success = true;
        $error = "0";
        
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'full_name' => 'required',
            'email' => 'required'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        $jsonObj = json_decode($request->checkboxes);

        try {
            DB::transaction(function () use ($request, $user_id, $id, $jsonObj) {
                $User = User::findOrFail($id);

                $User->username = $request->username;
                $User->full_name = $request->full_name;
                $User->email = $request->email;
                // $User->user_type_id = $request->user_type_id;

                foreach($jsonObj as $json){
                    foreach($json as $j){
                        $RoleUser = RoleUser::where([['user_id', '=', $id],
                            ['role_id', '=', $j->role]])->first();
                        if($RoleUser != null){
                            if(!$j->checked){
                                $RoleUser->is_deleted = 1;
                                $RoleUser->update();
                            }else{
                                $RoleUser->is_deleted = 0;
                                $RoleUser->update();
                            }
                        }else{
                            if($j->checked){
                                RoleUser::create([
                                    'role_id' => $j->role,
                                    'user_id' => $id
                                ]);
                            }
                        }
                    }
                }

                $User->update();
                
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

        return redirect('users')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // auth()->user()->authorizeRoles(['admin']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $User = User::findOrFail($id);
                $User->is_deleted = 1;

                $RoleUser = RoleUser::where('user_id', $id)->get();
                foreach($RoleUser as $ru){
                    $ru->is_deleted = 1;
                    $ru->update();
                }

                $User->update();
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

        return redirect('users')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        // auth()->user()->authorizeRoles(['admin']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $User = User::findOrFail($id);
                $User->is_deleted = 0;

                $RoleUser = RoleUser::where('user_id', $id)->get();
                foreach($RoleUser as $ru){
                    $ru->is_deleted = 0;
                    $ru->update();
                }

                $User->update();
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

        return redirect('users')->with(['mesage' => $msg, 'icon' => $icon]);
    }
}
