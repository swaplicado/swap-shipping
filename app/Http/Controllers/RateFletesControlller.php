<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sat\Municipalities;

class RateFletesControlller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $carrier_id = auth()->user()->carrier()->first()->id_carrier;
        $rates = \DB::table('f_carriers_rate_flete')
                    ->where([['carrier_id', $carrier_id],['is_official',1]])
                    ->select('veh_type_id','mun_id','state_id','rate')
                    ->get();

        $mun = Municipalities::join('sat_states', 'sat_municipalities.state_id', '=', 'sat_states.id')
                            ->select(
                                'sat_municipalities.id',
                                'sat_municipalities.municipality_name',
                                'sat_municipalities.state_id',
                                'sat_states.state_name'
                                )
                            ->get();
        $vehiclesTypes = \DB::table('f_vehicles_keys')->get();
        foreach($mun as $m){
            foreach($vehiclesTypes as $v){
                $data = $rates->where('mun_id',$m->id)->where('veh_type_id',$v->id_key)->pluck('rate');
                $name = (string)$v->key_code;
                if(isset($data[0])){
                    $m->$name = $data[0];
                }else{
                    $m->$name = null;
                }
            }
        }
        return view('catalogos/rates/index',['mun' => $mun, 'veh' => $vehiclesTypes]);
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
