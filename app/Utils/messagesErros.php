<?php namespace App\Utils;

class messagesErros {
    public static function sqlMessageError($message){
        $flag = false;
        $n = 0;
        for($i = 0; $i<strlen($message); $i++){
            if($message[$i] == "'" && $flag){
                $n = $i;
                break;
            }
            if($message[$i] == "'"){
                $flag = true;
            }
        }
        $string = substr($message, 0, $n+1);
        return $string;
    }
}