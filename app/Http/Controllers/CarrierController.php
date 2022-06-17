<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use App\Core\FinkokCore;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Validator;
use Auth;
use stdClass;
use App\User;
use App\Role;
use App\UserVsTypes;
use App\Models\Sat\ProdServ;
use App\Models\Sat\Tax_regimes;
use Illuminate\Support\Facades\Hash;
use App\Utils\messagesErros;
use App\Utils\CfdUtils;
use App\Models\Certificate;
use App\Models\Manifest;
use Illuminate\Validation\Rule;

class CarrierController extends Controller
{
    private $attributeNames = array(
        'email' => 'Email',
        'password' => 'Contraseña',
        'tax_regimes' => 'Régimen fiscal',
        'fullname' => 'Nombre completo',
        'RFC' => 'RFC',
        'contact1' => 'Contacto 1',
        'telephone1' => 'Teléfono',
        'prod_serv' => 'Concepto',
        'delega_CFDI' => 'delegación del CFDI'
    );


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // auth()->user()->authorizeRoles(['user', 'admin']);
        auth()->user()->authorizePermission(['211']);
        if(auth()->user()->isCarrier()){
            $data = Carrier::where('id_carrier', auth()->user()->carrier()->first()->id_carrier)->get();
        } else if (auth()->user()->isAdmin() || auth()->user()->isClient()){
            $data = Carrier::get();    
        }
        
        $data->each(function ($data) {
            $data->tax_regime;
        });
        
        foreach($data as $d){
            $d->Carrier = new stdClass();
            $d->Carrier->id_carrier = $d->id_carrier;
            $d->Carrier->fullname = $d->fullname;
        }

        $carriers = Carrier::where('is_deleted', 0)->select('id_carrier','fullname')->get();

        return view('ship/carriers/index', ['data' => $data, 'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // auth()->user()->authorizeRoles(['user', 'admin']);
        auth()->user()->authorizePermission(['212']);
        $data = new Carrier;
        $data->tax_regime = new Tax_regimes;
        $data->prod_serv = new ProdServ;
        $data->users = null;
        $tax_regimes = Tax_regimes::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $prod_serv = ProdServ::where('is_active', 1)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        return view('ship/carriers/create', ['data' => $data, 'tax_regimes' => $tax_regimes, 'prod_serv' => $prod_serv]);
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
        auth()->user()->authorizePermission(['212']);
        $success = true;
        $error = "0"; 

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'tax_regimes' => 'required|not_in:0',
            'fullname' => 'required',
            'RFC' => ['required', 'unique:f_carriers,fiscal_id'],
            'contact1' => 'required',
            'telephone1' => 'required',
            'prod_serv' => 'required|not_in:0'
        ]);

        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();

        $values = json_decode($request->post('tax_regimes'));
        $tr_name = $values->name;
        $tr_id = $values->id;
        
        $prodServ = json_decode($request->post('prod_serv'));
        $ps_name = $prodServ->name;
        $ps_id = $prodServ->id;
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        try {
            DB::transaction(function () use ($request, $user_id, $tr_id, $ps_id) {
                $user = User::create([
                    'username' => mb_strtoupper($request->fullname, 'UTF-8'),
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'full_name' => mb_strtoupper($request->fullname, 'UTF-8'),
                    'user_type_id' => 3,
                    'is_carrier' => 1
                ]);
        
                $user->roles()->attach(Role::where('id', 3)->first());
                $user->tempPass = $request->password;
                
                is_null($request->carrier_stamp) ? $request->carrier_stamp = 0 : "";
                $carrier = Carrier::create([
                    'fullname' => mb_strtoupper($request->fullname, 'UTF-8'),
                    'comercial_name' => mb_strtoupper($request->comercial_name, 'UTF-8'),
                    'fiscal_id' => mb_strtoupper($request->RFC, 'UTF-8'),
                    'tax_regimes_id' => $tr_id,
                    'contact1' => mb_strtoupper($request->contact1, 'UTF-8'),
                    'telephone1' => $request->telephone1,
                    'contact2' => mb_strtoupper($request->contact2, 'UTF-8'),
                    'telephone2' => $request->telephone2,
                    'prod_serv_id' => $ps_id,
                    'usr_new_id' => $user_id,
                    'usr_upd_id' => $user_id,
                    'carrier_stamp' => $request->carrier_stamp
                ]);
                
                $UserVsTypes = UserVsTypes::create([
                    'carrier_id' => $carrier->id_carrier,
                    'user_id' => $user->id,
                    'is_principal' => 1
                ]);
                $user->sendEmailVerificationNotification();
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

        return redirect('carriers')->with(['message' => $msg, 'icon' => $icon]);
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
        auth()->user()->authorizePermission(['213']);
        auth()->user()->carrierAutorization($id);
        $data = Carrier::findOrFail($id);
        if(!is_null($data)){
            $data->users;
            $data->tax_regime;
            $data->prod_serv;
        }
        
        $tax_regimes = Tax_regimes::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $prod_serv = ProdServ::where('is_active', 1)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        
        return view('ship/carriers/edit', ['data' => $data, 'tax_regimes' => $tax_regimes, 'prod_serv' => $prod_serv]);
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
        auth()->user()->authorizePermission(['213']);
        auth()->user()->carrierAutorization($id);
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'RFC' => ['required', Rule::unique('f_carriers','fiscal_id')->ignore($id, 'id_carrier')],
            'tax_regimes' => 'required|not_in:0',
            'prod_serv' => 'required|not_in:0'
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
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        $values = json_decode($request->post('tax_regimes'));
        $tr_name = $values->name;
        $tr_id = $values->id;

        $prodServ = json_decode($request->post('prod_serv'));
        $ps_name = $prodServ->name;
        $ps_id = $prodServ->id;

        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($request, $user_id, $id, $tr_id, $ps_id) {
                $carrier = Carrier::findOrFail($id);
                $user = User::findOrFail($carrier->users()->first()->id);

                $carrier->fullname = mb_strtoupper($request->fullname, 'UTF-8');
                $carrier->comercial_name = mb_strtoupper($request->comercial_name, 'UTF-8');
                $carrier->fiscal_id = mb_strtoupper($request->RFC, 'UTF-8');
                $carrier->tax_regimes_id = $tr_id;
                $carrier->prod_serv_id = $ps_id;
                $carrier->usr_upd_id = $user_id;

                $user->username = mb_strtoupper($request->fullname, 'UTF-8');
                $user->full_name = mb_strtoupper($request->fullname, 'UTF-8');
                if(!is_null($request->editEmail)){
                    if($user->email != $request->email){
                        $user->email = $request->email;
                        $user->email_verified_at = null;
                        $user->sendEmailVerificationNotification();
                    }
                }

                $carrier->update();
                $user->update();
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

        return redirect('carriers')->with(['message' => $msg, 'icon' => $icon]);

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
        auth()->user()->authorizePermission(['214']);
        auth()->user()->carrierAutorization($id);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $carrier = Carrier::findOrFail($id);
                $user = User::findOrFail($carrier->users()->first()->id);

                $carrier->is_deleted = 1;
                $carrier->usr_upd_id = $user_id;

                $user->is_deleted = 1;

                $UserVsTypes = UserVsTypes::where('carrier_id', $carrier->id_carrier)->firstOrFail();
                $UserVsTypes->is_deleted = 1;

                $carrier->update();
                $user->update();
                $UserVsTypes->update();
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

        return redirect('carriers')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function recover($id) 
    {
        // auth()->user()->authorizeRoles(['user', 'admin']);
        auth()->user()->authorizePermission(['215']);
        auth()->user()->carrierAutorization($id);
        $success = true;
        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($id, $user_id) {
                $carrier = Carrier::findOrFail($id);
                $user = User::findOrFail($carrier->users()->first()->id);

                $carrier->is_deleted = 0;
                $carrier->usr_upd_id = $user_id;

                $user->is_deleted = 0;

                $UserVsTypes = UserVsTypes::where('carrier_id', $carrier->id_carrier)->firstOrFail();
                $UserVsTypes->is_deleted = 0;

                $carrier->update();
                $user->update();
                $UserVsTypes->update();
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

        return redirect('carriers')->with(['message' => $msg, 'icon' => $icon]);
    }

    public function editFiscalData(Request $request, $id){
        auth()->user()->authorizePermission(['213']);
        auth()->user()->carrierAutorization($id);
        $data = Carrier::where('id_carrier', $id)->first();
        $data->users;
        $data->tax_regime;
        $data->prod_serv;

        $certificates = \DB::table('f_certificates AS c')
                                ->join('users AS unew', 'unew.id', '=', 'c.usr_new_id')
                                ->join('users AS uupd', 'uupd.id', '=', 'c.usr_upd_id')
                                ->select('c.*', 'unew.username as unew_username', 'uupd.username as uupd_username')
                                ->where('c.carrier_id', $id)
                                ->orderBy('c.dt_valid_to', 'DESC')
                                ->orderBy('c.dt_valid_from', 'DESC')
                                ->get();

        $tax_regimes = Tax_regimes::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $prod_serv = ProdServ::where('is_active', 1)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');

        $bManifestSigned = Manifest::where('carrier_id', $id)->where('is_signed', true)->count() > 0;
        
        return view('ship/carriers/fiscalData', [
                                                    'data' => $data, 
                                                    'tax_regimes' => $tax_regimes, 
                                                    'prod_serv' => $prod_serv,
                                                    'certificates' => $certificates,
                                                    'id' => $id,
                                                    'bManifestSigned' => $bManifestSigned
                                                ]);
    }

    public function updateFiscalData(Request $request, $id)
    {
        // auth()->user()->authorizeRoles(['user', 'admin']);
        auth()->user()->authorizePermission(['213']);
        auth()->user()->carrierAutorization($id);
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'RFC' => ['required', Rule::unique('f_carriers','fiscal_id')->ignore($id, 'id_carrier')],
            'tax_regimes' => 'required|not_in:0',
            'prod_serv' => 'required|not_in:0',
            'delega_CFDI' => 'required'
        ]);

        $validator->setAttributeNames($this->attributeNames);
        $validator->validate();
        
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        $values = json_decode($request->post('tax_regimes'));
        $tr_name = $values->name;
        $tr_id = $values->id;

        $prodServ = json_decode($request->post('prod_serv'));
        $ps_name = $prodServ->name;
        $ps_id = $prodServ->id;

        $user_id = (auth()->check()) ? auth()->user()->id : null;
        try {
            DB::transaction(function () use ($request, $user_id, $id, $tr_id, $ps_id) {
                $carrier = Carrier::findOrFail($id);
                
                $carrier->fiscal_id = mb_strtoupper($request->RFC, 'UTF-8');
                $carrier->tax_regimes_id = $tr_id;
                $carrier->prod_serv_id = $ps_id;
                $carrier->usr_upd_id = $user_id;

                $delega_stamp = 0;
                $delega_edit_stamp = 0;
                switch ($request->delega_CFDI) {
                    case 1:
                        $delega_edit_stamp = 1;
                        break;
                    case 2:
                        $delega_stamp = 1;
                        break;
                    default:
                        break;
                }
                $carrier->delega_stamp = $delega_stamp;
                $carrier->delega_edit_stamp = $delega_edit_stamp;
                $carrier->update();
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

        return redirect(route('editar_carrierFiscalData', ['id' => $id]))->with(['message' => $msg, 'icon' => $icon]);

    }

    /**
     * Registra al cliente en la plataforma de Finkok, 
     * después guarda el log y elimina los certificados
     * 
     * @param  Request $request
     * @return Response
     */
    public function storeCertificate(Request $request)    
    {
        $filePc = $request->file('pc');
        $fileNamePc = $filePc->getClientOriginalName();
        $fileExtensionPc = $filePc->getClientOriginalExtension();
        $filePathPc = $filePc->getRealPath();
        $destinationPath = env('DEST_PATH');

        $carrier = Carrier::find($request->carrier);

        $urlPc = Storage::putFileAs($destinationPath, new File($filePathPc), $carrier->fiscal_id.'_.cer');

        $certificate = CfdUtils::getCerData($urlPc);

        $oCarrier = Carrier::where('fiscal_id', $certificate->fiscalId)->first();

        if ($oCarrier == null) {
            Storage::disk('local')->delete($urlPc);
            return response()->json(['error' => 'El RFC del emisor en el certificado no existe en la base de datos'], 400);
        }

        if (auth()->user()->isCarrier()) {
            if (auth()->user()->carrier->fiscal_id != $oCarrier->fiscal_id) {
                Storage::disk('local')->delete($urlPc);
                return response()->json(['error' => 'El RFC del emisor en el certificado no corresponde al RFC del emisor del usuario'], 400);
            }
        }
        else if (auth()->user()->isDriver()) {
            if (auth()->user()->driver->Carrier->fiscal_id != $oCarrier->fiscal_id) {
                Storage::delete($urlPc);
                return response()->json(['error' => 'El RFC del emisor en el certificado no corresponde al RFC del emisor del usuario'], 400);
            }
        }

        $filePv = $request->file('pv');
    
        $fileNamePv = $filePv->getClientOriginalName();
        $fileExtensionPv = $filePv->getClientOriginalExtension();
        $filePathPv = $filePv->getRealPath();

        $urlPv = Storage::putFileAs($destinationPath, new File($filePathPv), $oCarrier->fiscal_id.'_.key');

        $response = FinkokCore::regCertificates($urlPc, $urlPv, $request->pw, $oCarrier->fiscal_id);

        if (! $response['success']) {
            Storage::disk('local')->delete($urlPc);
            Storage::disk('local')->delete($urlPv);
            
            return redirect(route('editar_carrierFiscalData', ['id' => $oCarrier->id_carrier]))->with(['message' => $response['message'], 'icon' => 'error']);
        }

        $message = 'El certificado fue registrado correctamente';
        if ($response['message'] == "Account Created successfully") {
            $oCertificate = new Certificate();
            $oCertificate->dt_valid_from = $certificate->fromDate;
            $oCertificate->dt_valid_to = $certificate->expDate;
            $oCertificate->cert_number = $certificate->certificateNumber;
            $oCertificate->pswrd = CfdUtils::encryptPass($request->pw);
            $oCertificate->carrier_id = $oCarrier->id_carrier;
            $oCertificate->usr_new_id = auth()->user()->id;
            $oCertificate->usr_upd_id = auth()->user()->id;
    
            $oCertificate->save();
        }

        CfdUtils::encryptFile(Storage::disk('local')->path($urlPc), substr(Storage::disk('local')->path($urlPc), 0, strlen(Storage::disk('local')->path($urlPc)) - 4).'cer.enc', env('FL_KEY'));
        CfdUtils::encryptFile(Storage::disk('local')->path($urlPv), substr(Storage::disk('local')->path($urlPv), 0, strlen(Storage::disk('local')->path($urlPv)) - 4).'key.enc', env('FL_KEY'));

        Storage::disk('local')->delete($urlPc);
        Storage::disk('local')->delete($urlPv);

        return redirect(route('editar_carrierFiscalData', ['id' => $oCarrier->id_carrier]))->with(['message' => $response['message'], 'icon' => 'success']);
    }

    public function signManifest(Request $request)
    {
        $oCarrier = Carrier::find($request->id_carrier);

        if ($oCarrier == null) {
            return redirect()->back()->with(['message' => 'No se encontró el transportista', 'icon' => 'error']);
        }

        if (! isset($request->is_signed) || $request->is_signed == null) {
            return redirect()->back()->with(['message' => 'No se confirmó la firma de manifiesto', 'icon' => 'error']);
        }

        $oSignManifest = new Manifest();
        $oSignManifest->is_signed = true;
        $oSignManifest->signed_at = date('Y-m-d H:i:s');
        $oSignManifest->signed_by = auth()->user()->id;
        $oSignManifest->carrier_id = $oCarrier->id_carrier;

        $oSignManifest->save();

        return redirect(route('editar_carrierFiscalData', ['id' => $oCarrier->id_carrier]))
                    ->with(
                        [
                            'message' => 'El manifiesto fue firmado correctamente', 
                            'icon' => 'success'
                        ]
                    );
    }
}
