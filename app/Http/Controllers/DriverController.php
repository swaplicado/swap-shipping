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
use App\UserPivot;
use Auth;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // auth()->user()->authorizeRoles(['admin']);
        $data = Driver::get();
        $data->each(function ($data) {
            $data->FAddress;
            $data->sat_FAddress;
            $data->User;
        });

        return view('ship/drivers/index', ['data' => $data]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        $data = new Driver;
        $data->FAddress = new FAddress;
        $tp_figures = TpFigure::pluck('id', 'description');
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');
        $countrys = FiscalAddress::orderBy('description', 'ASC')->pluck('id', 'description');
        $states = States::pluck('id', 'state_name');

        return view('ship/drivers/create', [
            'data' => $data, 'tp_figures' => $tp_figures, 'carriers' => $carriers,
            'countrys' => $countrys, 'states' => $states
        ]);
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
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'fullname' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'RFC' => 'required',
            'licence' => 'required',
            'tp_figure' => 'required|not_in:0',
            'carrier' => 'required|not_in:0',
            'country' => 'required|not_in:0',
            'zip_code' => 'required',
            'state' => 'required|not_in:0',
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        $validator->validate();
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        $values = json_decode($request->post('state'));
        $sta_name = $values->name;
        $sta_id = $values->id;

        try {
            DB::transaction(function () use ($sta_id, $sta_name, $user_id, $request) {
                $user = User::create([
                    'username' => $request->username,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'full_name' => $request->fullname,
                    'user_type_id' => 4,
                    'is_driver' => 1
                ]);
        
                $user->roles()->attach(Role::where('id', 4)->first());

                $tf = Driver::create([
                    'fullname' => $request->fullname,
                    'fiscal_id' => $request->RFC,
                    'fiscal_fgr_id' => $request->RFC_ex,
                    'driver_lic' => $request->licence,
                    'tp_figure_id' => $request->tp_figure,
                    'fis_address_id' => $request->country,
                    'carrier_id' => $request->carrier,
                    'usr_new_id' => $user_id,
                    'usr_upd_id' => $user_id
                ]);

                $address = FAddress::create([
                    'telephone' => $request->telephone,
                    'street' => $request->street,
                    'street_num_ext' => $request->street_num_ext,
                    'street_num_int' => $request->street_num_int,
                    'neighborhood' => $request->neighborhood,
                    'reference' => $request->reference,
                    'locality' => $request->locality,
                    'state' => $sta_name,
                    'zip_code' => $request->zip_code,
                    'trans_figure_id' => $tf->id_trans_figure,
                    'country_id' => $request->country,
                    'state_id' => $sta_id,
                    'usr_new_id' => $user_id,
                    'usr_upd_id' => $user_id
                ]);

                $UserPivot = UserPivot::create([
                    'trans_figure_id' => $tf->id_trans_figure,
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
            $msg = "Error al guardar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('drivers')->with(['mesage' => $msg, 'icon' => $icon]);
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
        $data = Driver::where('id_trans_figure', $id)->get();
        $data->each(function ($data) {
            $data->FAddress;
            $data->User;
        });
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
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'fullname' => 'required',
            'email' => ['required', 'string', 'email', 'max:255'],
            'RFC' => 'required',
            'licence' => 'required',
            'tp_figure' => 'required|not_in:0',
            'carrier' => 'required|not_in:0',
            'country' => 'required|not_in:0',
            'zip_code' => 'required',
            'state' => 'required|not_in:0'
        ]);
        $validator->validate();

        $success = true;
        $error = "0";

        $values = json_decode($request->post('state'));
        $sta_name = $values->name;
        $sta_id = $values->id;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($sta_id, $sta_name, $request, $id, $user_id) {
                $tf = Driver::findOrFail($id);
                $address = FAddress::where('trans_figure_id', $id)->firstOrFail();

                $tf->fullname = $request->fullname;
                $tf->fiscal_id = $request->RFC;
                $tf->fiscal_fgr_id = $request->RFC_ex;
                $tf->driver_lic = $request->licence;
                $tf->tp_figure_id = $request->tp_figure;
                $tf->fis_address_id = $request->country;
                $tf->carrier_id = $request->carrier;
                $tf->usr_upd_id = $user_id;

                $address->telephone = $request->telephone;
                $address->street = $request->street;
                $address->street_num_ext = $request->street_num_ext;
                $address->street_num_int = $request->street_num_int;
                $address->neighborhood = $request->neighborhood;
                $address->reference = $request->reference;
                $address->locality = $request->locality;
                $address->state = $sta_name;
                $address->zip_code = $request->zip_code;
                $address->country_id = $request->country;
                $address->state_id = $sta_id;
                $address->usr_upd_id = $user_id;

                $user = User::findOrFail($tf->usr_id);

                $user->username = $request->username;
                $user->full_name = $request->fullname;
                $user->email = $request->email;

                $tf->update();
                $address->update();
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
        return redirect('drivers')->with(['mesage' => $msg, 'icon' => $icon]);
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
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $tf = Driver::findOrFail($id);
                $address = FAddress::where('trans_figure_id', $id)->firstOrFail();
                $user = User::findOrFail($tf->usr_id);

                $tf->is_deleted = 1;
                $tf->usr_upd_id = $user_id;

                $address->is_deleted = 1;
                $address->usr_upd_id = $user_id;

                $user->is_deleted = 1;

                $userPivot = UserPivot::where('trans_figure_id', $tf->id_trans_figure)->firstOrFail();
                $userPivot->is_deleted = 1;

                $tf->update();
                $address->update();
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

        return redirect('drivers')->with(['mesage' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $tf = Driver::findOrFail($id);
                $address = FAddress::where('trans_figure_id', $id)->firstOrFail();
                $user = User::findOrFail($tf->usr_id);

                $tf->is_deleted = 0;
                $tf->usr_upd_id = $user_id;

                $address->is_deleted = 0;
                $address->usr_upd_id = $user_id;

                $user->is_deleted = 0;

                $userPivot = UserPivot::where('trans_figure_id', $tf->id_trans_figure)->firstOrFail();
                $userPivot->is_deleted = 1;

                $tf->update();
                $address->update();
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

        return redirect('drivers')->with(['mesage' => $msg, 'icon' => $icon]);
    }
}