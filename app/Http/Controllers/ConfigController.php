<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Utils\Configuration;
use App\Utils\CfdUtils;
use App\Utils\Encryption;
use App\Core\FinkokCore;
use Validator;
use App\Models\Sat\ProdServ;
use App\Models\Sat\Currencies;
use App\Models\Sat\Units;
use App\Models\Sat\Tax_regimes;
use App\Models\Sat\UsoCFDI;
use App\Models\Sat\Taxes;
use App\Models\Certificate;
use App\Models\Carrier;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        auth()->user()->authorizePermission(['011']);
        $data = Configuration::getConfigurations();
        return view('sys/config/index', ['data' => $data]);
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
    public function edit()
    {
        auth()->user()->authorizePermission(['013']);
        $data = Configuration::getConfigurations();

        $data->moneda = Currencies::where('key_code', $data->localCurrency)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->value('kd');
        $data->prod_serv = ProdServ::where('key_code', $data->cfdi4_0->claveServicio)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->value('kd');
        $data->unidad = Units::where('key_code', $data->cfdi4_0->claveUnidad)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->value('kd');
        $data->regimen = Tax_regimes::where('key_code', $data->cfdi4_0->regimenFiscalReceptor)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->value('kd');
        $data->usoCFDI = UsoCFDI::where('key_code', $data->cfdi4_0->usoCFDI)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->value('kd');
        $data->impuesto = Taxes::where('key_code', $data->cfdi4_0->objetoImp)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->value('kd');

        $currencies = Currencies::where('is_active', 1)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $prod_serv = ProdServ::where('is_active', 1)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $units = Units::where('is_active', 1)->selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $tax_regimes = Tax_regimes::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $usoCFDI = UsoCFDI::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        $taxes = Taxes::selectRaw('CONCAT(key_code, " - ", description) AS kd, id')->pluck('id', 'kd');
        

        return view('sys/config/edit', ['data' => $data, 'currencies' => $currencies,
            'prod_serv' => $prod_serv, 'currencies' => $currencies, 'units' => $units,
            'usoCFDI' => $usoCFDI, 'tax_regimes' => $tax_regimes,
            'taxes' => $taxes]);
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
        auth()->user()->authorizePermission(['013']);
        $success = true;
        $error = "0";

        $validator = Validator::make($request->all(), [
            'localCurrency' => 'required',
            'tarifaBase' => 'required',
            'tarifaBaseEscala' => 'required',
            'distanciaMinima' => 'required'
        ]);
        $validator->validate();
        
        $json = [];
        try {
            $json["email"] = $request->email;
            $json["localCurrency"] = Currencies::where('id', $request->localCurrency)->value('key_code');
            $json["tarifaBase"] = $request->tarifaBase;
            $json["tarifaBaseEscala"] = $request->tarifaBaseEscala;
            $json["distanciaMinima"] = $request->distanciaMinima;
            $json["claveServicio"] = ProdServ::where('id', $request->prod_serv)->value('key_code');
            $json["prodServDescripcion"] = ProdServ::where('id', $request->prod_serv)->value('description');
            $json["claveUnidad"] = Units::where('id', $request->units)->value('key_code');
            $json["simboloUnidad"] = Units::where('id', $request->units)->value('symbol');
            $json["rfc"] = strtoupper($request->rfc);
            $json["nombreReceptor"] = strtoupper($request->nombreReceptor);
            $json["domicilioFiscalReceptor"] = $request->domicilioFiscalReceptor;
            $json["regimenFiscalReceptor"] = Tax_regimes::where('id', $request->tax_regimes)->value('key_code');
            $json["usoCFDI"] = usoCFDI::where('id', $request->usoCFDI)->value('key_code');
            $json["objetoImp"] = Taxes::where('id', $request->taxes)->value('key_code');

            Configuration::updateConfiguration($json);
        } catch (\ErrorException $e) {
            $success = false;
            $error = $e->getMessage();
        }

        if ($success) {
            $msg = "Se actualizó el registro con éxito";
            $icon = "success";
        } else {
            $msg = "Error al actualizar el registro. Error: " . $error;
            $icon = "error";
        }

        return redirect('config')->with(['message' => $msg, 'icon' => $icon]);
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
