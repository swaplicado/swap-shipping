<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaxConfiguration;

class TaxConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * */
    public function index()
    {
        auth()->user()->authorizePermission(['701']);
        $lConfigurations = \DB::table('f_tax_configurations AS cfg')
                                ->join('sat_taxes AS tx', 'cfg.tax_id', '=', 'tx.id')
                                ->leftjoin('sat_tax_regimes AS reg', 'cfg.fiscal_regime_id', '=', 'reg.id')
                                ->leftjoin('sat_items AS cpt', 'cfg.concept_id', '=', 'cpt.id')
                                ->leftjoin('f_concepts_groups AS grp', 'cfg.group_id', '=', 'grp.id_group')
                                ->leftjoin('f_carriers AS crr', 'cfg.carrier_id', '=', 'crr.id_carrier')
                                ->whereNull('cfg.carrier_id');
        
        if (\Auth::user()->user_type_id == 1 || \Auth::user()->user_type_id == 2) {
            $lConfigurations = $lConfigurations->orWhere('cfg.carrier_id', ">=", 0);
        }
        else if (\Auth::user()->isCarrier()) {
            $lConfigurations = $lConfigurations->orWhere('cfg.carrier_id', \Auth::user()->carrier->id_carrier);
        }
        else {
            $lConfigurations = $lConfigurations->orWhere('cfg.carrier_id', \Auth::user()->driver->carrier_id);
        }

        $lConfigurations = $lConfigurations->select(['cfg.*', 
                                                    'tx.description AS tax_name', 
                                                    'reg.description AS regime_name', 
                                                    'cpt.description AS concept_name', 
                                                    'grp.group_name', 
                                                    'crr.fullname'])
                                            ->get();

        return view('ship.configurations.taxes.index')->with(["lConfigurations" => $lConfigurations]);
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @return \Illuminate\Http\Response
     * */
    public function create()
    {
        auth()->user()->authorizePermission(['702']);
        $lRegimes = \DB::table('sat_tax_regimes')->select('id', 'key_code', 'description')->get();
        $lConcepts = \DB::table('sat_prod_serv')->select('id', 'key_code', 'description')->where('is_active', true)->get();
        $lTaxes = \DB::table('sat_taxes')->select('id', 'key_code', 'description')->get();

        return view('ship.configurations.taxes.create')
                            ->with([
                                    'lRegimes' => $lRegimes,
                                    'lConcepts' => $lConcepts,
                                    'lTaxes' => $lTaxes
                                ]);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * */
    public function store(Request $request)
    {
        auth()->user()->authorizePermission(['702']);
        $oTaxConfiguration = new TaxConfiguration($request->all());
        $oTaxConfiguration->amount = 0;
        $oTaxConfiguration->is_deleted = false;

        if (\Auth::user()->isAdmin()) {
            $oTaxConfiguration->carrier_id = $request->carrier_id;
        }
        else if (\Auth::user()->isDriver()) {
            $oTaxConfiguration->carrier_id = \Auth::user()->driver->carrier_id;
        }
        else {
            $oTaxConfiguration->carrier_id = \Auth::user()->carrier->id_carrier;
        }
        
        $oTaxConfiguration->usr_new_id = \Auth::user()->id;
        $oTaxConfiguration->usr_upd_id = \Auth::user()->id;
        
        $oTaxConfiguration->save();

        return redirect()->route('config.taxes')
                            ->with(
                                ['message' => "La configuración se ha guardado con éxito.", 'icon' => "success"]
                            );
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * */
    public function edit($id)
    {
        auth()->user()->authorizePermission(['703']);
        $oTaxConfiguration = TaxConfiguration::find($id);

        $lRegimes = \DB::table('sat_tax_regimes')->select('id', 'key_code', 'description')->get();
        $lConcepts = \DB::table('sat_prod_serv')->select('id', 'key_code', 'description')->where('is_active', true)->get();
        $lTaxes = \DB::table('sat_taxes')->select('id', 'key_code', 'description')->get();

        return view('ship.configurations.taxes.edit')->with([
                                                                'lRegimes' => $lRegimes,
                                                                'lConcepts' => $lConcepts,
                                                                'lTaxes' => $lTaxes,
                                                                'oCfg' => $oTaxConfiguration
                                                            ]);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * */
    public function update(Request $request, $id)
    {
        auth()->user()->authorizePermission(['703']);
        $oTaxConfiguration = TaxConfiguration::find($id);
        $oTaxConfiguration->fill($request->all());
        $oTaxConfiguration->usr_upd_id = \Auth::user()->id;

        if (\Auth::user()->isAdmin() || \Auth::user()->user_type_id == 2) {
            $oTaxConfiguration->carrier_id = $request->carrier_id;
        }
        else if (\Auth::user()->isDriver()) {
            $oTaxConfiguration->carrier_id = \Auth::user()->driver->carrier_id;
        }
        else {
            $oTaxConfiguration->carrier_id = \Auth::user()->carrier->id_carrier;
        }

        $oTaxConfiguration->save();

        return redirect()->route('config.taxes')
                            ->with(
                                ['message' => "La configuración se ha modificado con éxito.", 
                                    'icon' => "success"]
                            );
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     * */
    public function destroy($id)
    {
        auth()->user()->authorizePermission(['704']);
        $oTaxConfiguration = TaxConfiguration::find($id);
        $oTaxConfiguration->is_deleted = true;
        $oTaxConfiguration->usr_upd_id = \Auth::user()->id;
        $oTaxConfiguration->save();

        return redirect()->route('config.taxes')
                            ->with(
                                ['message' => "La configuración se ha eliminado con éxito.",
                                    'icon' => "success"]
                            );

    }
}
