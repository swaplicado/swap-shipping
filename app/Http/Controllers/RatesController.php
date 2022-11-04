<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CarriersRate;
use App\Models\Sat\States;
use App\Models\Sat\Municipalities;
use App\Utils\GralUtils;

class RatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        auth()->user()->authorizePermission(['800']);
        if(auth()->user()->isCarrier()){
            $carrier_id = auth()->user()->carrier()->first()->id_carrier;
        } else if (auth()->user()->isAdmin() || auth()->user()->isClient()){
            $carrier_id = null;
        }
        $veh_types = \DB::table('f_vehicles_keys')->get();
        $title = 'Tarifas';

        switch ($id) {
            case 1:
                $rates = CarriersRate::where([['carrier_id', $carrier_id],['zone_mun_id', NULL],['zone_state_id', NULL]])->get();
                $mun = Municipalities::join('sat_states', 'sat_municipalities.state_id', '=', 'sat_states.id')
                ->select(
                    'sat_municipalities.id as mun_id',
                    'sat_municipalities.municipality_name',
                    'sat_municipalities.key_code as mun_key_code',
                    'sat_municipalities.state_id',
                    'sat_states.state_name',
                    'sat_states.key_code as st_key_code'
                    )
                ->get();

                foreach($mun as $m){
                    $m->local_foreign = GralUtils::getShipType($m->state_id, $m->mun_id, null);
                    if($m->state_id < 10){
                        $m->id_rate =  '0'.$m->state_id.$m->st_key_code.$m->mun_key_code;
                    }else{
                        $m->id_rate =  $m->state_id.$m->st_key_code.$m->mun_key_code;
                    }
                }
                $title = 'Tarifas municipio';
                return view('catalogos/rates/index',['rates' => $rates, 'mun' => $mun, 'veh' => $veh_types, 'id' => $id, 'title' => $title]);

            case 2:
                $rates = CarriersRate::where([['carrier_id', $carrier_id],['zone_mun_id', '!=', NULL]])->get();
                $mun = \DB::table('f_mun_zones')
                ->join('sat_municipalities', 'f_mun_zones.mun_id', '=', 'sat_municipalities.id')
                ->join('sat_states', 'f_mun_zones.state_id', '=', 'sat_states.id')
                ->where('f_mun_zones.origen_id', 1)
                ->select(
                    'f_mun_zones.id as id_mun_zone',
                    'f_mun_zones.origen_id',
                    'f_mun_zones.state_id',
                    'f_mun_zones.mun_id',
                    'f_mun_zones.zone',
                    'f_mun_zones.zone_digit',
                    'sat_municipalities.municipality_name',
                    'sat_municipalities.key_code as mun_key_code',
                    'sat_states.state_name',
                    'sat_states.key_code as st_key_code'
                    )
                ->get();
                foreach($mun as $m){
                    $m->local_foreign = GralUtils::getShipType($m->state_id, $m->mun_id, null);
                    if($m->state_id < 10){
                        $m->id_rate =  '0'.$m->state_id.$m->st_key_code.$m->mun_key_code.'-'.$m->zone_digit;
                    }else{
                        $m->id_rate =  $m->state_id.$m->st_key_code.$m->mun_key_code.'-'.$m->zone_digit;
                    }
                }
                $title = 'Tarifas zona municipio';
                return view('catalogos/rates/index',['rates' => $rates, 'mun' => $mun, 'veh' => $veh_types, 'id' => $id, 'title' => $title]);

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
                return view('catalogos/rates/index',['rates' => $rates, 'mun' => $mun, 'veh' => $veh_types, 'id' => $id, 'title' => $title]);
                
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
                return view('catalogos/rates/index',['rates' => $rates, 'mun' => $mun, 'veh' => $veh_types, 'id' => $id, 'title' => $title]);
                
            case 5:
                $rates = CarriersRate::where([
                    ['carrier_id', $carrier_id],
                    ['origen_id', 1],
                    ['is_reparto', 1],
                    ['zone_mun_id', NULL],
                    ['zone_state_id', NULL],
                    ['mun_id', NULL],
                    ['state_id', NULL]
                    ])->get();
                return view('catalogos/rates/indexReparto', ['rates' => $rates, 'veh' => $veh_types]);
                
                default:
                # code...
                break;
        }
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

    public function saveRate($carrier_id, $state_id, $mun_id, $id_zone_sta, $id_zone_mun, $veh_type_id, $tarifa, $rate_id = null, $local_foreign = null, $is_reparto = 0){
        $rate = CarriersRate::where([
            ['carrier_id',$carrier_id],
            ['state_id', $state_id],
            ['mun_id', $mun_id],
            ['zone_state_id', $id_zone_sta],
            ['zone_mun_id', $id_zone_mun],
            ['veh_type_id', $veh_type_id],
            ['Local_foreign', $local_foreign],
            ['is_reparto', $is_reparto],
            ['id_rate', $rate_id]
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
            $rate->Local_foreign = $local_foreign;
            $rate->is_reparto = $is_reparto;
            $rate->id_rate = $rate_id;
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
        auth()->user()->authorizePermission(['801']);
        if(auth()->user()->isCarrier()){
            $carrier_id = auth()->user()->carrier()->first()->id_carrier;
        } else if (auth()->user()->isAdmin() || auth()->user()->isClient()){
            $carrier_id = null;
        }

        if(isset($request->ratesIds)){
            if(sizeof($request->ratesIds) > 0){
                foreach($request->val as $index => $v){
                    $rate_id = $request->ratesIds[$index];
                    if($v[8] != null){
//$carrier_id, $state_id, $mun_id, $id_zone_sta, $id_zone_mun, $veh_type_id, $tarifa, $rate_id, $local_foreign, $is_reparto
                        $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 1, $v[8], $rate_id[0]);
                    }
                    if($v[9] != null){
                        $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 2, $v[9], $rate_id[1]);
                    }
                    if($v[10] != null){
                        $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 3, $v[10], $rate_id[2]);
                    }
                    if($v[11] != null){
                        $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 4, $v[11], $rate_id[3]);
                    }
                    if($v[12] != null){
                        $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 5, $v[12], $rate_id[4]);
                    }
                    if($v[13] != null){
                        $this->saveRate($carrier_id, $v[0], $v[1], $v[2], $v[3], 6, $v[13], $rate_id[5]);
                    }
                }
            }else{
                foreach($request->val as $index => $v){
                    if($v[8] != null){
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
            }
        }else{
            foreach($request->val as $index => $v){
                if($v[8] != null){
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
        }


        return response()->json($request->val);
    }

    public function storeReparto(Request $request){
        auth()->user()->authorizePermission(['801']);
        if(auth()->user()->isCarrier()){
            $carrier_id = auth()->user()->carrier()->first()->id_carrier;
        } else if (auth()->user()->isAdmin() || auth()->user()->isClient()){
            $carrier_id = null;
        }
        $val = $request->val;
        foreach($request->val as $v){
            if($v[2] != null){
//$carrier_id, $state_id, $mun_id, $id_zone_sta, $id_zone_mun, $veh_type_id, $tarifa, $rate_id, $local_foreign, $is_reparto
                $this->saveRate($carrier_id, null, null, null, null, 1, $v[2], null, $v[0], 1);
            }
            if($v[3] != null){
                $this->saveRate($carrier_id, null, null, null, null, 2, $v[3], null, $v[0], 1);
            }
            if($v[4] != null){
                $this->saveRate($carrier_id, null, null, null, null, 3, $v[4], null, $v[0], 1);
            }
            if($v[5] != null){
                $this->saveRate($carrier_id, null, null, null, null, 4, $v[5], null, $v[0], 1);
            }
            if($v[6] != null){
                $this->saveRate($carrier_id, null, null, null, null, 5, $v[6], null, $v[0], 1);
            }
            if($v[7] != null){
                $this->saveRate($carrier_id, null, null, null, null, 6, $v[7], null, $v[0], 1);
            }
        }
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
