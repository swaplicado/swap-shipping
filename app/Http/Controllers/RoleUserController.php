<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Role;
use App\RoleUser;
use App\Permission;
use App\RolePermission;
use Illuminate\Database\QueryException;
use App\Utils\messagesErros;
use Validator;

class RoleUserController extends Controller
{
    private $attributeNames = array(
        'name' => 'Nombre',
        'description' => 'Decripción'
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth()->user()->authorizePermission(['611']);
        $data = Role::get();
        $data->each(function ($data) {
            $data->RolePermissions;
        });
        
        return view('sys/roles/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        auth()->user()->authorizePermission(['612']);
        $data = new Role;

        $permissions = Permission::get();

        return view('sys/roles/create', ['data' => $data, 'permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required'
        ]);

        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        auth()->user()->authorizePermission(['612']);
        $success = true;
        $jsonObj = json_decode($request->checkboxes);
        try{
            DB::transaction(function () use ($request, $jsonObj) {
                $Role = Role::create([
                    'name' => strtoupper($request->name),
                    'description' => $request->description
                ]);
                foreach($jsonObj as $json){
                    foreach($json as $j){
                        if($j->checked){
                            RolePermission::create([
                                'role_id' => $Role->id,
                                'permission_id' => $j->permission
                            ]);
                        }
                    }
                }
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se guardó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al guardar el registro. Error: " . $error;
            $icon = "error";
        }
        return redirect('role')->with(['message' => $msg, 'icon' => $icon]);
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
        auth()->user()->authorizePermission(['613']);
        $data = Role::where('id', $id)->get();
        $data->each(function ($data) {
            $data->RolePermissions;
        });

        $permissions = Permission::get();

        return view('sys/roles/edit', ['data' => $data, 'permissions' => $permissions]);
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
        auth()->user()->authorizePermission(['613']);
        $success = true;
        $jsonObj = json_decode($request->checkboxes);
        try{
            DB::transaction(function () use ($request, $id, $jsonObj) {
                foreach($jsonObj as $json){
                    foreach($json as $j){
                        $RolePermission = RolePermission::where([['role_id', '=', $id],
                            ['permission_id', '=', $j->permission]])->first();
                        if($RolePermission != null){
                            if(!$j->checked){
                                $RolePermission->is_deleted = 1;
                                $RolePermission->update();
                            }else{
                                $RolePermission->is_deleted = 0;
                                $RolePermission->update();
                            }
                        }else{
                            if($j->checked){
                                RolePermission::create([
                                    'role_id' => $id,
                                    'permission_id' => $j->permission
                                ]);
                            }
                        }
                    }
                }
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
        return redirect('role')->with(['message' => $msg, 'icon' => $icon]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        auth()->user()->authorizePermission(['614']);
        $success = true;
        try{
            DB::transaction(function () use ($id) {
                $Role = Role::findOrFail($id);
                $Role->is_deleted = 1;
                
                $RoleUser = RoleUser::where('role_id', $id)->get();
                foreach($RoleUser as $ru){
                    $ru->is_deleted = 1;
                    $ru->update();
                }

                $RolePermission = RolePermission::where('role_id', $id)->get();
                foreach($RolePermission as $rp){
                    $rp->is_deleted = 1;
                    $rp->update();
                }
                
                $Role->update();
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
        return redirect('role')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function recover($id){
        auth()->user()->authorizePermission(['615']);
        $success = true;
        try{
            DB::transaction(function () use ($id) {
                $Role = Role::findOrFail($id);
                $Role->is_deleted = 0;
                
                $RoleUser = RoleUser::where('role_id', $id)->get();
                foreach($RoleUser as $ru){
                    $ru->is_deleted = 0;
                    $ru->update();
                }

                $RolePermission = RolePermission::where('role_id', $id)->get();
                foreach($RolePermission as $rp){
                    $rp->is_deleted = 0;
                    $rp->update();
                }
                
                $Role->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se reuperó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al recuperar el registro. Error: " . $error;
            $icon = "error";
        }
        return redirect('role')->with(['message' => $msg, 'icon' => $icon]);
    }
}
