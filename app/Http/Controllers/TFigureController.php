<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\FAddress;
use App\Models\FigureT;
use App\Models\Sat\FiscalAddress;
use App\Models\sat\States;
use App\Models\TpFigure;
use App\Utils\messagesErros;
use DB;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class TFigureController extends Controller
{
    private $attributeNames = array(
        'fullname' => 'Nombre completo',
        'RFC' => 'RFC',
        'tp_figure' => 'Tipo de figura de transporte',
        'country' => 'País',
        'carrier' => 'Transportista'
    );

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // auth()->user()->authorizeRoles(['admin']);
        auth()->user()->authorizePermission(['311']);
        if (auth()->user()->isCarrier()) {
            $data = FigureT::where('carrier_id', auth()->user()->carrier()->first()->id_carrier)
                ->where('tp_figure_id', '!=', 1)
                ->get();
        }
        else if (auth()->user()->isAdmin() || auth()->user()->isClient()) {
            $data = FigureT::where('tp_figure_id', '!=', 1)
                ->get();
        }

        $data->each(function ($data) {
            $data->FAddress;
            $data->sat_FAddress;
            $data->User;
        });

        $carriers = Carrier::where('is_deleted', 0)->select('id_carrier', 'fullname')->get();

        return view('ship/tfigures/index', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['312']);
        $data = new FigureT;
        $data->users = NULL;
        $data->is_new = 1;
        $tp_figures = TpFigure::whereIn('id', [2, 3])->pluck('id', 'description');
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');
        $countrys = FiscalAddress::orderBy('description', 'ASC')->pluck('id', 'description');
        $states = States::pluck('id', 'state_name');

        return view('ship/tfigures/create', [
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
        auth()->user()->authorizePermission(['312']);

        if (auth()->user()->isCarrier()) {
            $request->request->add(['carrier' => auth()->user()->carrier()->first()->id_carrier]);
        }
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'RFC' => 'required',
            'tp_figure' => 'required|not_in:0',
            'country' => 'required|not_in:0',
            'carrier' => 'required|not_in:0'
        ]);

        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        try {
            DB::transaction(function () use ($user_id, $request) {

                $oFigure = FigureT::create([
                    'fullname' => mb_strtoupper($request->fullname, 'UTF-8'),
                    'fiscal_id' => mb_strtoupper($request->RFC, 'UTF-8'),
                    'fiscal_fgr_id' => mb_strtoupper($request->RFC_ex, 'UTF-8'),
                    'driver_lic' => mb_strtoupper("", 'UTF-8'),
                    'tp_figure_id' => $request->tp_figure,
                    'fis_address_id' => $request->country,
                    'carrier_id' => $request->carrier,
                    'usr_new_id' => auth()->user()->id,
                    'usr_upd_id' => auth()->user()->id
                ]);

                $address = FAddress::create([
                    'telephone' => "",
                    'street' => mb_strtoupper("", 'UTF-8'),
                    'street_num_ext' => "",
                    'street_num_int' => "",
                    'neighborhood' => mb_strtoupper("", 'UTF-8'),
                    'reference' => "",
                    'locality' => "",
                    'state' => "",
                    'zip_code' => "",
                    'trans_figure_id' => $oFigure->id_trans_figure,
                    'country_id' => $request->country,
                    'state_id' => 1,
                    'usr_new_id' => auth()->user()->id,
                    'usr_upd_id' => auth()->user()->id
                ]);
            });
        }
        catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se guardó el registro con éxito";
            $icon = "success";
        }
        else {
            $msg = "Error al guardar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('tfigures')->with(['message' => $msg, 'icon' => $icon]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  FigureT $oFigure
     * 
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['313']);
        $data = FigureT::where([['id_trans_figure', $id], ['is_deleted', 0]])->first();
        auth()->user()->carrierAutorization($data->carrier_id);
        $data->FAddress;

        $tp_figures = TpFigure::whereIn('id', [2, 3])->pluck('id', 'description');
        $carriers = Carrier::where('is_deleted', 0)->orderBy('fullname', 'ASC')->pluck('id_carrier', 'fullname');
        $countrys = FiscalAddress::orderBy('description', 'ASC')->pluck('id', 'description');
        $states = States::pluck('id', 'state_name');

        return view('ship/tfigures/edit', [
            'data' => $data, 'tp_figures' => $tp_figures, 'carriers' => $carriers,
            'countrys' => $countrys, 'states' => $states
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  FigureT $oFigure
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['313']);

        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'RFC' => ['required', Rule::unique('f_trans_figures', 'fiscal_id')->ignore($id, 'id_trans_figure')],
        ]);

        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        $success = true;
        $error = "0";
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($request, $id, $user_id) {
                $oFigure = FigureT::findOrFail($id);
                auth()->user()->carrierAutorization($oFigure->carrier_id);
                $address = FAddress::where('trans_figure_id', $id)->firstOrFail();

                $oFigure->fullname = mb_strtoupper($request->fullname, 'UTF-8');
                $oFigure->fiscal_id = mb_strtoupper($request->RFC, 'UTF-8');
                $oFigure->fiscal_fgr_id = mb_strtoupper($request->RFC_ex, 'UTF-8');
                $oFigure->tp_figure_id = $request->tp_figure;
                $oFigure->fis_address_id = $request->country;
                $oFigure->usr_upd_id = $user_id;

                $address->telephone = "";
                $address->street = mb_strtoupper("", 'UTF-8');
                $address->street_num_ext = "";
                $address->street_num_int = "";
                $address->neighborhood = mb_strtoupper("", 'UTF-8');
                $address->reference = "";
                $address->locality = "";
                $address->state = "";
                $address->zip_code = "";
                $address->country_id = $request->country;
                $address->state_id = 1;
                $address->usr_upd_id = $user_id;

                $oFigure->update();
                $address->update();
            });
        }
        catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se actualizó el registro con éxito";
            $icon = "success";
        }
        else {
            $msg = "Error al actualizar el registro. Error: " . $error;
            $icon = "error";
        }
        return redirect('tfigures')->with(['message' => $msg, 'icon' => $icon]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  TpFigure $oFigure
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
                $oFigure = FigureT::findOrFail($id);
                auth()->user()->carrierAutorization($oFigure->carrier_id);
                $address = FAddress::where('trans_figure_id', $id)->firstOrFail();

                $oFigure->is_deleted = 1;
                $oFigure->usr_upd_id = $user_id;

                $address->is_deleted = 1;
                $address->usr_upd_id = $user_id;


                $oFigure->update();
                $address->update();
            });
        }
        catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se eliminó el registro con éxito";
            $icon = "success";
        }
        else {
            $msg = "Error al eliminar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('tfigures')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function recover($id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin', 'carrier']);
        auth()->user()->authorizePermission(['315']);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $oFigure = FigureT::findOrFail($id);
                auth()->user()->carrierAutorization($oFigure->carrier_id);
                $address = FAddress::where('trans_figure_id', $id)->firstOrFail();

                $oFigure->is_deleted = 0;
                $oFigure->usr_upd_id = $user_id;

                $address->is_deleted = 0;
                $address->usr_upd_id = $user_id;



                $oFigure->update();
                $address->update();
            });
        }
        catch (QueryException $e) {
            $success = false;
            $error = messagesErros::sqlMessageError($e->errorInfo[2]);
        }

        if ($success) {
            $msg = "Se recuperó el registro con éxito";
            $icon = "success";
        }
        else {
            $msg = "Error al recuperar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('tfigures')->with(['message' => $msg, 'icon' => $icon]);
    }
}
