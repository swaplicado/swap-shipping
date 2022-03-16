<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Driver;
use App\Models\Carrier;
use App\Models\TpFigure;
use App\Models\Sat\FiscalAddress;
use App\Models\Sat\States;
use App\Models\FAddress;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Validator;
use App\User;
use App\Role;
use App\UserVsTypes;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\Utils\messagesErros;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    private $attributeNames = array(
        'fullname' => 'Nombre completo',
        'email' => 'Email',
        'RFC' => 'RFC',
        'licence' => 'Licencia',
        'tp_figure' => 'Tipo de figura de transporte',
        'country' => 'País',
        'zip_code' => 'Código postal',
        'state' => 'Estado',
        'carrier' => 'Transportista',
        'rol' => 'Rol del chofer',
        'password' => 'Contraseña'
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // auth()->user()->authorizeRoles(['admin']);
        auth()->user()->authorizePermission(['311']);
        if(auth()->user()->isCarrier()){
            $data = Driver::where('carrier_id', auth()->user()->carrier()->first()->id_carrier)->get();
        } else if (auth()->user()->isAdmin() || auth()->user()->isClient()){
            $data = Driver::get();    
        }

        $data->each(function ($data) {
            $data->FAddress;
            $data->sat_FAddress;
            $data->User;
        });

        $carriers = Carrier::where('is_deleted', 0)->select('id_carrier','fullname')->get();

        return view('ship/drivers/index', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['312']);
        $data = new Driver;
        $data->FAddress = new FAddress;
        $data->users = NULL;
        $data->is_new = 1;
        $tp_figures = TpFigure::pluck('id', 'description');
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');
        $countrys = FiscalAddress::orderBy('description', 'ASC')->pluck('id', 'description');
        $states = States::pluck('id', 'state_name');

        return view('ship/drivers/create', [
            'data' => $data, 'tp_figures' => $tp_figures, 'carriers' => $carriers,
            'countrys' => $countrys, 'states' => $states]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['312']);
        if(auth()->user()->isCarrier()){
            $request->request->add(['carrier' => auth()->user()->carrier()->first()->id_carrier]);
        }
        $success = true;
        $error = "0";

        if ($request->is_with_user == 'on') {
            $validator = Validator::make($request->all(), [
                'fullname' => 'required',
                'email' => ['required', 'string', 'email', 'max:255', $request->is_with_user == 'on' ? 'unique:users' : ''],
                'RFC' => 'required',
                'licence' => 'required',
                'tp_figure' => 'required|not_in:0',
                'country' => 'required|not_in:0',
                'zip_code' => 'required',
                'state' => 'required|not_in:0',
                'carrier' => 'required|not_in:0',
                'rol' => 'required',
                'password' => ['required', 'string', 'min:8', 'confirmed']
            ]);
        } else{
            $validator = Validator::make($request->all(), [
                'fullname' => 'required',
                'RFC' => 'required',
                'licence' => 'required',
                'tp_figure' => 'required|not_in:0',
                'country' => 'required|not_in:0',
                'zip_code' => 'required',
                'state' => 'required|not_in:0',
                'carrier' => 'required|not_in:0'
            ]);
        }
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        $values = json_decode($request->post('state'));
        $sta_name = $values->name;
        $sta_id = $values->id;

        try {
            DB::transaction(function () use ($sta_id, $sta_name, $user_id, $request) {
                if ($request->is_with_user == 'on') {
                    $user = User::create([
                        'username' => mb_strtoupper($request->fullname, 'UTF-8'),
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'full_name' => mb_strtoupper($request->fullname, 'UTF-8'),
                        'user_type_id' => 4
                    ]);

                    $rol = null;
                    switch ($request->rol) {
                        case '1':
                            $rol = 6;
                            break;
                        case '2':
                            $rol = 5;
                            break;
                        case '3':
                            $rol = 4;
                            break;
                        default:
                            break;
                    }

                    $user->roles()->attach(Role::where('id', $rol)->first());
                    $user->tempPass = $request->password;
                    $user->sendEmailVerificationNotification();
                }

                $Driver = Driver::create([
                    'fullname' => mb_strtoupper($request->fullname, 'UTF-8'),
                    'fiscal_id' => mb_strtoupper($request->RFC, 'UTF-8'),
                    'fiscal_fgr_id' => mb_strtoupper($request->RFC_ex, 'UTF-8'),
                    'driver_lic' => mb_strtoupper($request->licence, 'UTF-8'),
                    'tp_figure_id' => $request->tp_figure,
                    'fis_address_id' => $request->country,
                    'carrier_id' => $request->carrier,
                    'usr_new_id' => auth()->user()->id,
                    'usr_upd_id' => auth()->user()->id
                ]);

                $address = FAddress::create([
                    'telephone' => $request->telephone,
                    'street' => mb_strtoupper($request->street, 'UTF-8'),
                    'street_num_ext' => $request->street_num_ext,
                    'street_num_int' => $request->street_num_int,
                    'neighborhood' => mb_strtoupper($request->neighborhood, 'UTF-8'),
                    'reference' => $request->reference,
                    'locality' => $request->locality,
                    'state' => $sta_name,
                    'zip_code' => $request->zip_code,
                    'trans_figure_id' => $Driver->id_trans_figure,
                    'country_id' => $request->country,
                    'state_id' => $sta_id,
                    'usr_new_id' => auth()->user()->id,
                    'usr_upd_id' => auth()->user()->id
                ]);

                if ($request->is_with_user == 'on') {
                    $UserVsTypes = UserVsTypes::create([
                        'trans_figure_id' => $Driver->id_trans_figure,
                        'user_id' => $user->id
                    ]);
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

        return redirect('drivers')->with(['message' => $msg, 'icon' => $icon]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Driver  $Driver
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Driver  $Driver
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['313']);
        $data = Driver::where([['id_trans_figure', $id], ['is_deleted', 0]])->first();
        auth()->user()->carrierAutorization($data->carrier_id);
        $data->FAddress;
        if(!is_null($data->UserVsTypes()->first())){
            $data->users;
        }else{
            $data->users = null;
            $data->is_new = 0;
        }
        $tp_figures = TpFigure::pluck('id', 'description');
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');
        $countrys = FiscalAddress::orderBy('description', 'ASC')->pluck('id', 'description');
        $states = States::pluck('id', 'state_name');
        return view('ship/drivers/edit', [
            'data' => $data, 'tp_figures' => $tp_figures, 'carriers' => $carriers,
            'countrys' => $countrys, 'states' => $states
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Driver  $Driver
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['313']);

        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'RFC' => ['required', Rule::unique('f_trans_figures','fiscal_id')->ignore($id, 'id_trans_figure')],
            'licence' => 'required',
            'tp_figure' => 'required|not_in:0',
            'country' => 'required|not_in:0',
            'zip_code' => 'required',
            'state' => 'required|not_in:0'
        ]);
        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        if(!is_null($request->editEmail)){
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users']
            ]);
            $validator->setAttributeNames($this->attributeNames);
            $validator->validate();
        }

        $success = true;
        $error = "0";

        $values = json_decode($request->post('state'));
        $sta_name = $values->name;
        $sta_id = $values->id;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($sta_id, $sta_name, $request, $id, $user_id) {
                $Driver = Driver::findOrFail($id);
                auth()->user()->carrierAutorization($Driver->carrier_id);
                $address = FAddress::where('trans_figure_id', $id)->firstOrFail();

                $Driver->fullname = mb_strtoupper($request->fullname, 'UTF-8');
                $Driver->fiscal_id = mb_strtoupper($request->RFC, 'UTF-8');
                $Driver->fiscal_fgr_id = mb_strtoupper($request->RFC_ex, 'UTF-8');
                $Driver->driver_lic = mb_strtoupper($request->licence, 'UTF-8');
                $Driver->tp_figure_id = $request->tp_figure;
                $Driver->fis_address_id = $request->country;
                $Driver->usr_upd_id = $user_id;

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
                $address->usr_upd_id = $user_id;

                if(!is_null($Driver->UserVsTypes()->first())){
                    $user = User::findOrFail($Driver->users()->first()->id);
                    $user->username = mb_strtoupper($request->fullname, 'UTF-8');
                    $user->full_name = mb_strtoupper($request->fullname, 'UTF-8');
                    if(!is_null($request->editEmail)){
                        if($user->email != $request->email){
                            $user->email = $request->email;
                            $user->email_verified_at = null;
                            $user->sendEmailVerificationNotification();
                        }
                    }    
                    $user->update();
                }

                $Driver->update();
                $address->update();
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
        return redirect('drivers')->with(['message' => $msg, 'icon' => $icon]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Driver  $Driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['314']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $Driver = Driver::findOrFail($id);
                auth()->user()->carrierAutorization($Driver->carrier_id);
                $address = FAddress::where('trans_figure_id', $id)->firstOrFail();
                if(!is_null($Driver->users())){
                    $user = User::findOrFail($Driver->users()->first()->id);
                    $user->is_deleted = 1;
                    $UserVsTypes = UserVsTypes::where('trans_figure_id', $Driver->id_trans_figure)->firstOrFail();
                    $UserVsTypes->is_deleted = 1;
                }

                $Driver->is_deleted = 1;
                $Driver->usr_upd_id = $user_id;

                $address->is_deleted = 1;
                $address->usr_upd_id = $user_id;
                

                $Driver->update();
                $address->update();
                if(!is_null($Driver->users())){
                    $user->update();
                    $UserVsTypes->update();
                }
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

        return redirect('drivers')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['315']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $Driver = Driver::findOrFail($id);
                auth()->user()->carrierAutorization($Driver->carrier_id);
                $address = FAddress::where('trans_figure_id', $id)->firstOrFail();
                if(!is_null($Driver->users())){
                    $user = User::findOrFail($Driver->users()->first()->id);
                    $user->is_deleted = 0;
                    $UserVsTypes = UserVsTypes::where('trans_figure_id', $Driver->id_trans_figure)->firstOrFail();
                    $UserVsTypes->is_deleted = 0;
                }

                $Driver->is_deleted = 0;
                $Driver->usr_upd_id = $user_id;

                $address->is_deleted = 0;
                $address->usr_upd_id = $user_id;



                $Driver->update();
                $address->update();
                if(!is_null($Driver->users())){
                    $user->update();
                    $UserVsTypes->update();
                }
            });
        } catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se recuperó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al recuperar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('drivers')->with(['message' => $msg, 'icon' => $icon]);
    }
}
