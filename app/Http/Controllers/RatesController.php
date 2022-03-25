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
        // $data = \DB::table('sat_municipalities')
        //             ->join('sat_states', 'sat_municipalities.state_id', '=', 'sat_states.id')
        //             ->leftJoin('f_carriers_rate', function ($leftJoin) use($carrier_id) {
        //                 $leftJoin->on([['f_carriers_rate.state_id','sat_municipalities.state_id'],['f_carriers_rate.mun_id','sat_municipalities.id']])
        //                       ->where([['f_carriers_rate.carrier_id', $carrier_id],['f_carriers_rate.is_distribution',0]]);
        //             })
        //             ->select('sat_municipalities.id','sat_municipalities.municipality_name','sat_municipalities.state_id','sat_states.state_name','f_carriers_rate.rate')
        //             ->get();
        // $mun = Municipalities::get();

        // $data = Municipalities::join('sat_states', 'sat_municipalities.state_id', '=', 'sat_states.id')
        //     ->leftJoin('f_carriers_rates', function ($leftJoin) use($carrier_id) {
        //         $leftJoin->on([['f_carriers_rates.state_id','sat_municipalities.state_id'],['f_carriers_rates.mun_id','sat_municipalities.id']])
        //                 ->where([['f_carriers_rates.carrier_id', $carrier_id],['is_official',1],['is_reparto',0]]);
        //     })
        //     ->select(
        //         'sat_municipalities.id',
        //         'sat_municipalities.municipality_name',
        //         'sat_municipalities.state_id',
        //         'sat_states.state_name',
        //         'f_carriers_rates.veh_type_id',
        //         'f_carriers_rates.rate'
        //         )
        //     ->get();

        $mun = Municipalities::join('sat_states', 'sat_municipalities.state_id', '=', 'sat_states.id')
                            ->select('sat_municipalities.id','sat_municipalities.municipality_name','sat_municipalities.state_id','sat_states.state_name')
                            ->get();
        $rates = CarriersRate::where([['carrier_id', $carrier_id],['is_reparto',0],['is_official',1]])->get();
        $veh = \DB::table('f_vehicles_keys')->get();
        foreach($mun as $m){
            $coll = $rates->where('id',$m->id);
            $m->rates = $coll;
        }
        // // $res = $data->where('id',1238)->where('veh_type_id', 1);
        // dd($data);
        return view('catalogos/rates/index', ['mun' => $mun, 'veh' => $veh]);
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
        $arr = array();
        $carrier_id = auth()->user()->carrier()->first()->id_carrier;

        for($i = 0; $i<sizeof($request->in); $i++){
            if($i % 2 != 0){
                $values = json_decode($request->in[$i]);
                $values->rate = $request->in[$i - 1];
                array_push($arr, $values);
            }
        }

        foreach($arr as $a){
            $rate = CarriersRate::where([
                                ['carrier_id', $carrier_id],
                                ['state_id',$a->state_id],
                                ['mun_id',$a->mun_id],
                                ['is_distribution', 0],
                                ['is_official', 1]
                            ])->first();
            
            if(!is_null($rate)){
                $rate->rate = $a->rate;
                $rate->update();
            }else{
                $rate->carrier_id = $carrier_id;
                $rate->state_id = $a->state_id;
                $rate->mun_id = $a->mun_id;
                $rate->is_distribution = 0;
                $rate->is_official = 1;
                $rate->rate = $a->rate;
                $rate->save();
            }
        }

        
        return response()->json(200);
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
