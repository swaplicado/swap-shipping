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
    public function indexRateFlete($id)
    {
        $carrier_id = auth()->user()->carrier()->first()->id_carrier;
        $veh_types = \DB::table('f_vehicles_keys')->get();
        $title = 'Tarifas';

        switch ($id) {
            case 1:
                $rates = CarriersRate::where([['carrier_id', $carrier_id],['zone_mun_id', NULL],['zone_state_id', NULL]])->get();
                $mun = Municipalities::join('sat_states', 'sat_municipalities.state_id', '=', 'sat_states.id')
                ->select('sat_municipalities.id as mun_id','sat_municipalities.municipality_name','sat_municipalities.state_id','sat_states.state_name')
                ->get();
                $title = 'Tarifas municipio';
                break;
            case 2:
                $rates = CarriersRate::where([['carrier_id', $carrier_id],['zone_mun_id', '!=', NULL]])->get();
                $mun = \DB::table('f_mun_zones')
                ->join('sat_municipalities',
                        'f_mun_zones.mun_id', '=', 'sat_municipalities.id',)
                ->join('sat_states', 'f_mun_zones.state_id', '=', 'sat_states.id')
                ->where('f_mun_zones.origen_id', 1)
                ->select(
                    'f_mun_zones.id as id_mun_zone',
                    'f_mun_zones.origen_id',
                    'f_mun_zones.state_id',
                    'f_mun_zones.mun_id',
                    'f_mun_zones.zone',
                    'sat_municipalities.municipality_name',
                    'sat_states.state_name'
                    )
                ->get();
                $title = 'Tarifas zona municipio';
                break;
            case 3:
                $rates = CarriersRate::where([['carrier_id', $carrier_id],['zone_state_id', '!=', NULL]])->get();
                $mun = \DB::table('f_state_zones')
                ->join('sat_states', 'f_state_zones.state_id', '=', 'sat_states.id')
                ->where('f_state_zones.origen_id', 1)
                ->select(
                    'f_state_zones.id as id_state_zone',
                    'f_state_zones.origen_id',
                    'f_state_zones.state_id',
                    'f_state_zones.zone',
                    'sat_states.state_name'
                )
                ->get();
                $title = 'Tarifas zona estado';
                break;
            case 4:
                $rates = CarriersRate::where([
                    ['carrier_id', $carrier_id],
                    ['state_id', '!=', NULL],
                    ['zone_state_id', NULL],
                    ['zone_mun_id', NULL],
                    ['mun_id', NULL]
                    ])
                    ->get();
                $mun = States::whereBetween('id',[1,32])
                ->select(
                    'id as state_id',
                    'state_name'
                    )
                ->get();
                $title = 'Tarifas estado';
                break;
                default:
                # code...
                break;
        }

        return view('catalogos/rates/index',['rates' => $rates, 'mun' => $mun, 'veh' => $veh_types, 'id' => $id, 'title' => $title]);
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

    public function saveRate($carrier_id, $state_id, $mun_id, $id_zone_sta, $id_zone_mun, $veh_type_id, $tarifa){
        $rate = CarriersRate::where([
            ['carrier_id',$carrier_id],
            ['state_id', $state_id],
            ['mun_id', $mun_id],
            ['zone_state_id', $id_zone_sta],
            ['zone_mun_id', $id_zone_mun],
            ['veh_type_id', $veh_type_id]
        ])->first();
         
        if(!is_null($rate)){
            $rate->rate = $tarifa;
            $rate->update();
        }else{
            $rate = new CarriersRate;
            $rate->carrier_id = $carrier_id;
            $rate->origen_id = 1;
            $rate->veh_type_id = $veh_type_id;
            $rate->state_id = $state_id;
            $rate->zone_state_id = $id_zone_sta;
            $rate->mun_id = $mun_id;
            $rate->zone_mun_id = $id_zone_mun;
            $rate->rate = $tarifa;
            $rate->save();
        }
    }

    /**
     * Guarda la tarifa ingresada,
     * los index son:
     * v[0] = id del estado,
     * v[1] = id del municipio,
     * v[2] = id zona estado,
     * v[3] = id zona municipio,
     * v[4] = nombre estado,
     * v[5] = nombre municipio,
     * v[6] = nombre zona estado,
     * v[7] = nombre zona municipio,
     * v[8] = tarifa Trailer 48,
     * v[9] = tarifa Trailer 53,
     * v[10] = tarifa Mudancero,
     * v[11] = tarifa Thorton,
     * v[12] = tarifa Camioneta grande,
     * v[13] = tarifa Camioneta chica
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $carrier_id = auth()->user()->carrier()->first()->id_carrier;

        foreach($request->val as $v){
            if($v[8] != null){
                //saveRate(carrier_id, state_id, mun_id, id_zone_sta, id_zone_mun, veh_type_id, tarifa)
                $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 1, $v[8]);
            }
            if($v[9] != null){
                $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 2, $v[9]);
            }
            if($v[10] != null){
                $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 3, $v[10]);
            }
            if($v[11] != null){
                $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 4, $v[11]);
            }
            if($v[12] != null){
                $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 5, $v[12]);
            }
            if($v[13] != null){
                $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 6, $v[13]);
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
