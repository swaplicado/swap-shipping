<?php namespace App\Utils;

use Illuminate\Support\Facades\DB;
use Auth;
use App\User;
use App\UserVsTypes;
use App\Utils\Configuration;

class MailUtils {
    
    public static function getClientMail(){
        $data = Configuration::getConfigurations();
        return explode(";", $data->email);
    }

    public static function getCarrierMail(){
        $type = UserVsTypes::where([['is_deleted', 0], ['user_id', auth()->user()->id]])->first();
        if(!is_null($type->carrier_id)){
            $carrier = $type->carrier()->first();
            $user = $carrier->users()->first();
        }else if (!is_null($type->trans_figure_id)){
            $driver = $type->driver()->first();
            $carrier = $driver->Carrier()->first();
            $user = $carrier->users()->first();
        }

        return $user->email;
    }

    public static function getUserMail(){
        return auth()->user()->email;
    }

    public static function getMails(){
        $mails = [];
        array_push($mails, MailUtils::getClientMail());
        $type = UserVsTypes::where([['is_deleted', 0], ['user_id', auth()->user()->id]])->first();
        if($type->is_principal){
            array_push($mails, MailUtils::getUserMail());
        } else {
            array_push($mails, MailUtils::getCarrierMail());
            array_push($mails, MailUtils::getUserMail());
        }
        return $mails;
    }

    public static function getComercialName(){
        $type = UserVsTypes::where([['is_deleted', 0], ['user_id', auth()->user()->id]])->first();
        if(!is_null($type->carrier_id)){
            $carrier = $type->carrier()->first();
        }else if (!is_null($type->trans_figure_id)){
            $driver = $type->driver()->first();
            $carrier = $driver->Carrier()->first();
        }

        return $carrier->comercial_name;
    }
}