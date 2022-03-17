<?php namespace App\Utils;

class GralUtils {
    
    public static function arrayToObject($array) {
        $obj = new \stdClass;
        foreach($array as $k => $v) {
           if(strlen($k)) {
              if(is_array($v)) {
                 $obj->{$k} = GralUtils::arrayToObject($v); //RECURSION
              } else {
                 $obj->{$k} = $v;
              }
           }
        }
        return $obj;
    }
}