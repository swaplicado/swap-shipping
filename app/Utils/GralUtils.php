<?php namespace App\Utils;

use App\Models\Sat\States;
use App\Models\Sat\Municipalities;
use App\Models\CarriersRate;

class GralUtils {

   public static function arrayToObject($array)
   {
      $obj = new \stdClass;
      foreach ($array as $k => $v) {
         if (strlen($k)) {
               if (is_array($v)) {
                  $obj->{$k} = GralUtils::arrayToObject($v); //RECURSION
               } else {
                  $obj->{$k} = $v;
               }
         }
      }
      return $obj;
   }

   public static function getShipType($idState, $idMun, $idZipCode)
   {
      $shipType = '';

      $state = \DB::table('f_local_locations')
                  ->where('state_id', $idState)
                  ->first();

      if (!is_null($state)) {
         $shipType = "L";
      }
      else {
         $municipalitie = \DB::table('f_local_locations')
                              ->where('municipality_id', $idMun)
                              ->first();

         if (!is_null($municipalitie)) {
               $shipType = "L";
         }
         else {
               $zip_code = \DB::table('f_local_locations')
                              ->where('zip_code', $idZipCode)
                              ->first();

               if (!is_null($zip_code)) {
                  $shipType = "L";
               }
               else {
                  $shipType = "F";
               }
         }
      }
      return $shipType;
   }

   public static function getMunicipalityByCode(string $stateCode, string $municipalityCode)
   {
      $oMunicipality = \DB::table('sat_municipalities AS m')
                           ->join('sat_states AS s', 's.id', '=', 'm.state_id')
                           ->select('m.*')
                           ->where('s.key_code', $stateCode)
                           ->where('m.key_code', $municipalityCode)
                           ->first();

      return $oMunicipality;
   }

   public static function generateRateKey($state_key, $mun_key, $zip_code, $veh_type_id, $origen_id = 1){
      $state_id = States::where('key_code',$state_key)->value('id');
      $mun_id = Municipalities::where([['key_code',$mun_key],['state_id', $state_id]])->value('id');
      $zone_mun_cp_id = \DB::table('f_mun_zones_cp')->where('zip_code', $zip_code)->value('mun_zone_id');
      $zone_digit = \DB::table('f_mun_zones')->where('id', $zone_mun_cp_id)->value('zone_digit');

      if(!is_null($zone_digit)){
         $zone_digit = '-'.$zone_digit;
      }

      $local_foreign = GralUtils::getShipType($state_id, $mun_id, $zip_code);

      if($local_foreign == 'F'){
         $veh_digit = \DB::table('f_vehicles_keys')->where('id_key', $veh_type_id)->value('foreign_digit');
      }else{
         $veh_digit = \DB::table('f_vehicles_keys')->where('id_key', $veh_type_id)->value('local_digit');
      }

      if($state_id < 10){
         $rateKey = $local_foreign.$veh_digit.'0'.$state_id.$state_key.$mun_key.$zone_digit;
      }else{
         $rateKey = $local_foreign.$veh_digit.$state_id.$state_key.$mun_key.$zone_digit;
      }
      return $rateKey;
   }

   public static function getInfoRate($carrier_id, $state_key, $mun_key, $zip_code, $veh_type_id, $is_reparto = 0, $local_foreign = null, $origen_id = 1){
      $state_id = States::where('key_code',$state_key)->value('id');
      $mun_id = Municipalities::where([['key_code',$mun_key],['state_id', $state_id]])->value('id');

      if($is_reparto == 1){
         $local_foreign = GralUtils::getShipType($state_id, $mun_id, $zip_code);
      }
      
      $zone_mun_cp_id = \DB::table('f_mun_zones_cp')->where('zip_code', $zip_code)->value('mun_zone_id');
      $zone_mun_id = \DB::table('f_mun_zones')->where('id', $zone_mun_cp_id)->value('id');
      $zone_st_mun_id = \DB::table('f_state_zones_mun')->where('mun_id', $mun_id)->value('state_zone_id');
      $zone_st_id = \DB::table('f_state_zones')->where('id', $zone_st_mun_id)->value('id');

      $rate = CarriersRate::where([
         ['carrier_id', $carrier_id],
         ['origen_id', $origen_id],
         ['is_reparto', $is_reparto],
         ['local_foreign', $local_foreign],
         ['zone_mun_id', $zone_mun_id],
         ['mun_id', $mun_id],
         ['zone_state_id', null],
         ['state_id', $state_id],
         ['veh_type_id', $veh_type_id],
         ])->first();

      if(is_null($rate)){
         $rate = CarriersRate::where([
            ['carrier_id', null],
            ['origen_id', $origen_id],
            ['is_reparto', $is_reparto],
            ['local_foreign', $local_foreign],
            ['zone_mun_id', $zone_mun_id],
            ['mun_id', $mun_id],
            ['zone_state_id', null],
            ['state_id', $state_id],
            ['veh_type_id', $veh_type_id],
            ])->first();
            
            if(is_null($rate)){
               $rate = CarriersRate::where([
                  ['carrier_id', $carrier_id],
                  ['origen_id', $origen_id],
                  ['is_reparto', $is_reparto],
                  ['local_foreign', $local_foreign],
                  ['zone_mun_id', null],
                  ['mun_id', $mun_id],
                  ['zone_state_id', null],
                  ['state_id', $state_id],
                  ['veh_type_id', $veh_type_id],
                  ])->first();
                  
                  if(is_null($rate)){
                     $rate = CarriersRate::where([
                        ['carrier_id', null],
                        ['origen_id', $origen_id],
                        ['is_reparto', $is_reparto],
                        ['local_foreign', $local_foreign],
                        ['zone_mun_id', null],
                        ['mun_id', $mun_id],
                        ['zone_state_id', null],
                        ['state_id', $state_id],
                        ['veh_type_id', $veh_type_id],
                        ])->first();

                        if(is_null($rate)){
                           $rate = CarriersRate::where([
                              ['carrier_id', $carrier_id],
                              ['origen_id', $origen_id],
                              ['is_reparto', $is_reparto],
                              ['local_foreign', $local_foreign],
                              ['zone_mun_id', null],
                              ['mun_id', null],
                              ['zone_state_id', $zone_st_id],
                              ['state_id', $state_id],
                              ['veh_type_id', $veh_type_id],
                              ])->first();

                              if(is_null($rate)){
                                 $rate = CarriersRate::where([
                                    ['carrier_id', null],
                                    ['origen_id', $origen_id],
                                    ['is_reparto', $is_reparto],
                                    ['local_foreign', $local_foreign],
                                    ['zone_mun_id', null],
                                    ['mun_id', null],
                                    ['zone_state_id', $zone_st_id],
                                    ['state_id', $state_id],
                                    ['veh_type_id', $veh_type_id],
                                    ])->first();

                                    if(is_null($rate)){
                                       $rate = CarriersRate::where([
                                          ['carrier_id', $carrier_id],
                                          ['origen_id', $origen_id],
                                          ['is_reparto', $is_reparto],
                                          ['local_foreign', $local_foreign],
                                          ['zone_mun_id', null],
                                          ['mun_id', null],
                                          ['zone_state_id', null],
                                          ['state_id', $state_id],
                                          ['veh_type_id', $veh_type_id],
                                          ])->first();

                                          if(is_null($rate)){
                                             $rate = CarriersRate::where([
                                                ['carrier_id', null],
                                                ['origen_id', $origen_id],
                                                ['is_reparto', $is_reparto],
                                                ['local_foreign', $local_foreign],
                                                ['zone_mun_id', null],
                                                ['mun_id', null],
                                                ['zone_state_id', null],
                                                ['state_id', $state_id],
                                                ['veh_type_id', $veh_type_id],
                                                ])->first();
                                          }
                                    }
                              }
                        }
                  }
            }
      }
      
      return $rate;
   }

   public static function saveRates($carrier_id, $shipType, $veh_type_id, $locations, $conceptos, $origen_id = 1){
      for($i = 0; $i < count($conceptos); $i++){
         $state_id = States::where('key_code', $locations[$i + 1]['domicilio']['estado'])->value('id');
                  
         $mun_id = Municipalities::where([
                                 ['key_code', $locations[$i + 1]['domicilio']['municipio']],
                                 ['state_id',$state_id]
                                    ])
                                 ->value('id');
         if($i < count($conceptos) - 1){
            if($conceptos[$i]['isOfficialRate']){
               if(!is_null($locations[$i + 1]['domicilio']['estado']) && !is_null($locations[$i + 1]['domicilio']['municipio'])){
                  $zip_code = $locations[$i + 1]['domicilio']['codigoPostal'];
                  $local_foreign = GralUtils::getShipType($state_id, $mun_id, $zip_code);
                  $oRate = CarriersRate::where([
                                          ['carrier_id', $carrier_id],
                                          ['origen_id', $origen_id],
                                          ['is_reparto', 1],
                                          ['local_foreign', $local_foreign],
                                          ['zone_mun_id', null],
                                          ['mun_id', null],
                                          ['zone_state_id', null],
                                          ['state_id', null],
                                          ['veh_type_id', $veh_type_id]
                                          ])->first();
                  
                  if(!is_null($oRate)){
                     $oRate->rate = $conceptos[$i]['valorUnitario'];
                     $oRate->update();
                  }else{
                     $oRate = new CarriersRate;
                     $oRate->carrier_id = $carrier_id;
                     $oRate->origen_id = 1;
                     $oRate->veh_type_id = $veh_type_id;
                     $oRate->is_reparto = 1;
                     $oRate->rate = $conceptos[$i]['valorUnitario'];
                     $oRate->save();
                  }
               }
            }
         }else{
            if($conceptos[$i]['isOfficialRate']){
               if(!is_null($locations[$i + 1]['domicilio']['estado']) && !is_null($locations[$i + 1]['domicilio']['municipio'])){
                  $zip_code = $locations[$i + 1]['domicilio']['codigoPostal'];
                  $zone_mun_cp_id = \DB::table('f_mun_zones_cp')->where('zip_code', $zip_code)->value('mun_zone_id');
                  $zone_mun_id = \DB::table('f_mun_zones')->where('id', $zone_mun_cp_id)->value('id');
                  $zone_st_mun_id = \DB::table('f_state_zones_mun')->where('mun_id', $mun_id)->value('state_zone_id');
                  $zone_st_id = \DB::table('f_state_zones')->where('id', $zone_st_mun_id)->value('id');
                  
                  $oRate = CarriersRate::where([
                                          ['carrier_id', $carrier_id],
                                          ['origen_id', $origen_id],
                                          ['is_reparto', 0],
                                          ['local_foreign', null],
                                          ['zone_mun_id', $zone_mun_id],
                                          ['mun_id', $mun_id],
                                          ['zone_state_id', $zone_st_id],
                                          ['state_id', $state_id],
                                          ['veh_type_id', $veh_type_id]
                                          ])->first();
                  
                  if(!is_null($oRate)){
                     $oRate->rate = $conceptos[$i]['valorUnitario'];
                     $oRate->update();
                  }else{
                     $oRate = new CarriersRate;
                     $oRate->carrier_id = $carrier_id;
                     $oRate->origen_id = 1;
                     $oRate->veh_type_id = $veh_type_id;
                     $oRate->state_id = $state_id;
                     $oRate->zone_state_id = $zone_st_id;
                     $oRate->mun_id = $mun_id;
                     $oRate->zone_mun_id = $zone_mun_id;
                     $oRate->id_rate = $conceptos[$i]['oCustomAttributes']->rateCode;
                     $oRate->rate = $conceptos[$i]['valorUnitario'];
                     $oRate->save();
                  }
               }
            }
         }
      }
   }
}