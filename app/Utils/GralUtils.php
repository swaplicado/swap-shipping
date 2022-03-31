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

   public static function saveRates($carrier_id, $ship_type, $vehKeyId, $locations, $conceptos){
      // dd($carrier_id, $vehKeyId, $locations, $conceptos);
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
                  $oRate = CarriersRate::where([
                                       ['carrier_id', $carrier_id],
                                       ['ship_type', $ship_type],
                                       ['veh_type_id', $vehKeyId],
                                       ['state_id', $state_id],
                                       ['mun_id', $mun_id],
                                       ['is_reparto', 1]
                                       ])->first();
                  
                  if(!is_null($oRate)){
                     $oRate->carrier_id = $carrier_id;
                     $oRate->ship_type = $ship_type;
                     $oRate->veh_type_id = $vehKeyId;
                     $oRate->mun_id = $mun_id;
                     $oRate->state_id = $state_id;
                     $oRate->rate = $conceptos[$i]['valorUnitario'];
                     $oRate->is_official = $conceptos[$i]['isOfficialRate'];
                     $oRate->is_reparto = 1;
                     $oRate->update();
                  }else{
                     $oRate = new CarriersRate;
                     $oRate->carrier_id = $carrier_id;
                     $oRate->ship_type = $ship_type;
                     $oRate->veh_type_id = $vehKeyId;
                     $oRate->mun_id = $mun_id;
                     $oRate->state_id = $state_id;
                     $oRate->rate = $conceptos[$i]['valorUnitario'];
                     $oRate->is_official = $conceptos[$i]['isOfficialRate'];
                     $oRate->is_reparto = 1;
                     $oRate->save();
                  }
               }
            }
         }else{
            if($conceptos[$i]['isOfficialRate']){
               if(!is_null($locations[$i + 1]['domicilio']['estado']) && !is_null($locations[$i + 1]['domicilio']['municipio'])){
                  $oRate = CarriersRate::where([
                                       ['carrier_id', $carrier_id],
                                       ['ship_type', $ship_type],
                                       ['veh_type_id', $vehKeyId],
                                       ['state_id', $state_id],
                                       ['mun_id', $mun_id],
                                       ['is_reparto', 0]
                                       ])->first();
                  
                  if(!is_null($oRate)){
                     $oRate->carrier_id = $carrier_id;
                     $oRate->ship_type = $ship_type;
                     $oRate->veh_type_id = $vehKeyId;
                     $oRate->mun_id = $mun_id;
                     $oRate->state_id = $state_id;
                     $oRate->rate = $conceptos[$i]['valorUnitario'];
                     $oRate->is_official = $conceptos[$i]['isOfficialRate'];
                     $oRate->is_reparto = 0;
                     $oRate->update();
                  }else{
                     $oRate = new CarriersRate;
                     $oRate->carrier_id = $carrier_id;
                     $oRate->ship_type = $ship_type;
                     $oRate->veh_type_id = $vehKeyId;
                     $oRate->mun_id = $mun_id;
                     $oRate->state_id = $state_id;
                     $oRate->rate = $conceptos[$i]['valorUnitario'];
                     $oRate->is_official = $conceptos[$i]['isOfficialRate'];
                     $oRate->is_reparto = 0;
                     $oRate->save();
                  }
               }
            }
         }
      }
   } 

   public static function getRate($carrier_id, $ship_type, $state_id, $mun_id, $veh_type_id, $is_reparto){
      $rate = 0;
      if(!is_null($carrier_id) && !is_null($state_id) && !is_null($mun_id) && !is_null($veh_type_id) && !is_null($is_reparto)){
         $rate = CarriersRate::where([
                        ['carrier_id', $carrier_id],
                        ['ship_type', $ship_type],
                        ['veh_type_id', $veh_type_id],
                        ['state_id', $state_id],
                        ['mun_id', $mun_id],
                        ['is_official', 1],
                        ['is_reparto', $is_reparto]
                     ])
                     ->value('rate');
         
         if(!is_null($rate)){
            return $rate;
         }else{
            return 0;
         }
      }else{
         return $rate;
      }
   }
}