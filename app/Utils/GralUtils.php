<?php namespace App\Utils;

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
}