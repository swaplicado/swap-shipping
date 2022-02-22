<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\Carrier;
use App\Models\BussinesParner;
use App\User;
use App\Role;
use App\RoleUser;
use App\UserVsTypes;
use App\Utils\messagesErros;
use App\Models\Sat\Tax_regimes;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;

class BussinesParnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth()->user()->authorizePermission(['241']);
        if(auth()->user()->isCarrier()){
            $data = auth()->user()->carrier()->first()->parners()->get();
        } else if (auth()->user()->isAdmin() || auth()->user()->isClient()){
            $data = UserVsTypes::where([['is_principal', 0], ['is_deleted', 0], ['carrier_id', '!=', null]])->get();
        }
        
        $carriers = Carrier::where('is_deleted', 0)->select('id_carrier','fullname')->get();
        return view('ship/carriers/parners/index', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        auth()->user()->authorizePermission(['242']);
        $data = null;
        return view('ship/carriers/parners/create', ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        auth()->user()->authorizePermission(['242']);
        if(auth()->user()->isCarrier()){
            $request->request->add(['carrier' => auth()->user()->carrier()->first()->id_carrier]);
        }
        $success = true;
        $error = "0";
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'fullname' => 'required',
            'carrier' => 'required|not_in:0'
        ]);

        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        
        try {
            DB::transaction(function () use ($request, $user_id) {
                $user = User::create([
                    'username' => strtoupper($request->fullname),
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'full_name' => strtoupper($request->fullname),
                    'user_type_id' => 3,
                    'is_carrier' => 1
                ]);
        
                $user->roles()->attach(Role::where('id', 3)->first());
                $user->sendEmailVerificationNotification();
                
                $UserVsTypes = UserVsTypes::create([
                    'carrier_id' => $request->carrier,
                    'user_id' => $user->id,
                    'is_principal' => 0
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

        return redirect('parners')->with(['message' => $msg, 'icon' => $icon]);
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
        auth()->user()->authorizePermission(['243']);
        $data = User::where('id', $id)->get();
        $carrier_id = UserVsTypes::where('user_id', $id)->value('carrier_id');
        auth()->user()->carrierAutorization($carrier_id);
        return view('ship/carriers/parners/edit', ['data' => $data]);
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
        auth()->user()->authorizePermission(['243']);
        $carrier_id = UserVsTypes::where('user_id', $id)->value('carrier_id');
        auth()->user()->carrierAutorization($carrier_id);
        $success = true;
        $error = "0";
        
        $validator = Validator::make($request->all(), [
            'fullname' => 'required'
        ]);

        $validator->validate();

        if(!is_null($request->editEmail)){
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
            ]);
            
            $validator->validate();
        }
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        try {
            DB::transaction(function () use ($request, $user_id, $id) {
                $User = User::findOrFail($id);

                $User->username = strtoupper($request->fullname);
                $User->full_name = strtoupper($request->fullname);
                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->email = $request->email;
                        $user->email_verified_at = null;
                        $user->sendEmailVerificationNotification();
                    }
                }
                
                $User->update();
                
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

        return redirect('parners')->with(['message' => $msg, 'icon' => $icon]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        auth()->user()->authorizePermission(['244']);
        $carrier_id = UserVsTypes::where('user_id', $id)->value('carrier_id');
        auth()->user()->carrierAutorization($carrier_id);
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

                $UserVsTypes = UserVsTypes::where('user_id', $id)->get();
                foreach($UserVsTypes as $ut){
                    $ut->is_deleted = 1;
                    $ut->update();
                }

                $User->update();
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

        return redirect('parners')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        auth()->user()->authorizePermission(['245']);
        $carrier_id = UserVsTypes::where('user_id', $id)->value('carrier_id');
        auth()->user()->carrierAutorization($carrier_id);
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

                $UserVsTypes = UserVsTypes::where('user_id', $id)->get();
                foreach($UserVsTypes as $ut){
                    $ut->is_deleted = 0;
                    $ut->update();
                }

                $User->update();
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se recuperó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al recuperó el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('parners')->with(['message' => $msg, 'icon' => $icon]);
    }
}
