<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\User;
use App\Models\Carrier;
use App\Models\Driver;
use App\Models\FAddress;
use App\Utils\messagesErros;
use Validator;

class ProfileController extends Controller
{
    private $attributeNames = array(
        'fullname' => 'Nombre completo',
        'email' => 'Email',
        'contact1' => 'Contacto 1',
        'telephone1' => 'teléfono 1',
        'new_password' => 'Nueva contraseña',
        'password_confirm' => 'Confirmar contraseña'
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = User::findOrFail(auth()->user()->id);
        return view('auth/profile/index', ['data' => $data]);
    }

    public function updateAdmin(Request $request, $id)
    {
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'fullname' => 'required'
        ]);
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        if(!is_null($request->newPassword)){
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|min:8',
                'password_confirm' => 'required|same:new_password'
            ]);
            $validator->setAttributeNames($this->attributeNames);
            $validator->validate();
        }

        if(!is_null($request->editEmail)){
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
            ]);
            $validator->setAttributeNames($this->attributeNames);
            $validator->validate();
        }

        try {
            DB::transaction(function () use ($request){
                $user = User::findOrFail(auth()->user()->id);

                $user->username = mb_strtoupper($request->fullname, 'UTF-8');
                $user->full_name = mb_strtoupper($request->fullname, 'UTF-8');
                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->email = $request->email;
                        $user->email_verified_at = null;
                    }
                }

                if(!is_null($request->newPassword)){
                    $user->password = \Hash::make($request->new_password);
                }

                $user->update();

                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->sendEmailVerificationNotification();
                    }
                }
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
            // $error = $e->errorInfo[2];
        }

        if ($success) {
            $msg = "Se actualizó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al actualizar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('profile')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function updateClient(Request $request, $id)
    {
        $success = true;
        $error = "0";
        
        $validator = Validator::make($request->all(), [
            'fullname' => 'required'
        ]);
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        if(!is_null($request->newPassword)){
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|min:8',
                'password_confirm' => 'required|same:new_password'
            ]);
            $validator->setAttributeNames($this->attributeNames);
            $validator->validate();
        }

        if(!is_null($request->editEmail)){
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
            ]);
            $validator->setAttributeNames($this->attributeNames);
            $validator->validate();
        }

        try {
            DB::transaction(function () use ($request){
                $user = User::findOrFail(auth()->user()->id);

                $user->username = mb_strtoupper($request->fullname, 'UTF-8');
                $user->full_name = mb_strtoupper($request->fullname, 'UTF-8');
                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->email = $request->email;
                        $user->email_verified_at = null;
                    }
                }
                if(!is_null($request->newPassword)){
                    $user->password = \Hash::make($request->new_password);
                }

                $user->update();
                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->sendEmailVerificationNotification();
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

        return redirect('profile')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function updateCarrier(Request $request, $id)
    {
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'contact1' => 'required',
            'telephone1' => 'required'
        ]);
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        if(!is_null($request->newPassword)){
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|min:8',
                'password_confirm' => 'required|same:new_password'
            ]);
            $validator->setAttributeNames($this->attributeNames);
            $validator->validate();
        }

        if(!is_null($request->editEmail)){
            $validator = Validator::make($request->all(), [
                'fullname' => 'required',
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'contact1' => 'required',
                'telephone1' => 'required'
            ]);
            $validator->setAttributeNames($this->attributeNames);
            $validator->validate();
        }

            $name = auth()->user()->carrier()->first()->fullname;
            $logo_name = null;
            $file = $request->file('logo');
            if(!is_null($file)){
                $logo_name = str_replace(' ', '_', $name);
                $logo_name = 'logo_'.$logo_name.'.'.$file->extension();
                $file->move('./logos',$logo_name);
            }

        try {
            DB::transaction(function () use ($request, $logo_name){
                $user = User::findOrFail(auth()->user()->id);
                $carrier = Carrier::findOrFail(auth()->user()->carrier()->first()->id_carrier);

                $user->username = mb_strtoupper($request->fullname, 'UTF-8');
                $user->full_name = mb_strtoupper($request->fullname, 'UTF-8');
                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->email = $request->email;
                        $user->email_verified_at = null;
                    }
                }
                if(!is_null($request->newPassword)){
                    $user->password = \Hash::make($request->new_password);
                }

                $carrier->fullname = mb_strtoupper($request->fullname, 'UTF-8');
                $carrier->comercial_name = mb_strtoupper($request->comercial_name, 'UTF-8');
                $carrier->contact1 = mb_strtoupper($request->contact1, 'UTF-8');
                $carrier->telephone1 = $request->telephone1;
                $carrier->contact2 = mb_strtoupper($request->contact2, 'UTF-8');
                $carrier->telephone2 = $request->telephone2;
                $carrier->logo = $logo_name;
                
                $carrier->update();
                $user->update();
                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->sendEmailVerificationNotification();
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

        return redirect('profile')->with(['message' => $msg, 'icon' => $icon]);

    }

    public function updateDriver(Request $request, $id)
    {
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'RFC' => 'required',
            'licence' => 'required',
            'country' => 'required|not_in:0',
            'zip_code' => 'required',
            'state' => 'required|not_in:0'
        ]);
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        if(!is_null($request->newPassword)){
            $validator = Validator::make($request->all(), [
                'new_password' => 'required|min:8',
                'password_confirm' => 'required|same:new_password'
            ]);
            $validator->setAttributeNames($this->attributeNames);
            $validator->validate();
        }

        if(!is_null($request->editEmail)){
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
            ]);
            $validator->setAttributeNames($this->attributeNames);
            $validator->validate();
        }

        $values = json_decode($request->post('state'));
        $sta_name = $values->name;
        $sta_id = $values->id;

        try {
            DB::transaction(function () use ($request, $sta_name, $sta_id){
                $user = User::findOrFail(auth()->user()->id);
                $driver = Driver::findOrFail(auth()->user()->driver()->first()->id_trans_figure);
                $address = FAddress::where('trans_figure_id', $driver->id_trans_figure)->firstOrFail();

                $user->username = mb_strtoupper($request->fullname, 'UTF-8');
                $user->full_name = mb_strtoupper($request->fullname, 'UTF-8');
                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->email = $request->email;
                        $user->email_verified_at = null;
                    }
                }
                if(!is_null($request->newPassword)){
                    $user->password = \Hash::make($request->new_password);
                }
                
                $driver->fullname = mb_strtoupper($request->fullname, 'UTF-8');
                $driver->fiscal_id = mb_strtoupper($request->RFC, 'UTF-8');
                $driver->fiscal_fgr_id = mb_strtoupper($request->RFC_ex, 'UTF-8');
                $driver->driver_lic = mb_strtoupper($request->licence, 'UTF-8');
                $driver->fis_address_id = $request->country;

                $address->telephone = $request->telephone;
                $address->street = mb_strtoupper($request->street, 'UTF-8');
                $address->street_num_ext = $request->street_num_ext;
                $address->street_num_int = $request->street_num_int;
                $address->neighborhood = mb_strtoupper($request->neighborhood, 'UTF-8');
                $address->reference = $request->reference;
                $address->locality = $request->locality;
                $address->state = $sta_name;
                $address->zip_code = $request->zip_code;
                $address->country_id = $request->country;
                $address->state_id = $sta_id;
                
                $driver->update();
                $address->update();
                $user->update();
                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->sendEmailVerificationNotification();
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

        return redirect('profile')->with(['message' => $msg, 'icon' => $icon]);
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
