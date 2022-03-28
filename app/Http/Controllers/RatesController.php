<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Utils\messagesErros;
use App\Models\CarriersRate;
use App\Models\Sat\States;
use App\Models\Sat\Municipalities;

class RatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRateFlete()
    {
        $carrier_id = auth()->user()->carrier()->first()->id_carrier;     
        $rates = CarriersRate::where('carrier_id', $carrier_id)->get();
        $mun = Municipalities::join('sat_states', 'sat_municipalities.state_id', '=', 'sat_states.id')
        ->select('sat_municipalities.id','sat_municipalities.municipality_name','sat_municipalities.state_id','sat_states.state_name')
        ->get();
        $veh_types = \DB::table('f_vehicles_keys')->get();
        return view('catalogos/rates/index',['rates' => $rates, 'mun' => $mun, 'veh' => $veh_types]);
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

    public function saveRate($mun_id, $veh_type_id, $state_id, $carrier_id, $value){
        $rate = CarriersRate::where([
            ['carrier_id',$carrier_id],
            ['state_id', $state_id],
            ['mun_id', $mun_id],
            ['veh_type_id', $veh_type_id]
        ])->first();

        if(!is_null($rate)){
            $rate->rate = $value;
            $rate->update();
        }else{
            $rate = new CarriersRate;
            $rate->carrier_id = $carrier_id;
            $rate->origen_id = 'OR000000';
            $rate->veh_type_id = $veh_type_id;
            $rate->state_id = $state_id;
            $rate->mun_id = $mun_id;
            $rate->rate = $value;
            $rate->save();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $carrier_id = auth()->user()->carrier()->first()->id_carrier;

        foreach($request->val as $v){
            if($v[4] != null){
                $this->saveRate($v[0], 1, $v[1], $carrier_id, $v[4]);
            }
            if($v[5] != null){
                $this->saveRate($v[0], 2, $v[1], $carrier_id, $v[5]);
            }
            if($v[6] != null){
                $this->saveRate($v[0], 3, $v[1], $carrier_id, $v[6]);
            }
            if($v[7] != null){
                $this->saveRate($v[0], 4, $v[1], $carrier_id, $v[7]);
            }
            if($v[8] != null){
                $this->saveRate($v[0], 5, $v[1], $carrier_id, $v[8]);
            }
            if($v[9] != null){
                $this->saveRate($v[0], 6, $v[1], $carrier_id, $v[9]);
            }
        }

        return response()->json($request->val);
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
