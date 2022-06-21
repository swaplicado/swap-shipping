<?php namespace App\Utils;

use App\User;
use App\UserVsTypes;
use App\Utils\Configuration;
use Auth;

class MailUtils
{

    public static function getClientMail()
    {
        $mails = [];
        $data = Configuration::getConfigurations();
        if (isset($data->email) && strlen($data->email) > 0) {
            $mails = explode(";", $data->email);
        }

        return $mails;
    }

    public static function getCarrierMail()
    {
        $type = UserVsTypes::where([['is_deleted', 0], ['user_id', auth()->user()->id]])->first();
        if (!is_null($type->carrier_id)) {
            $carrier = $type->carrier()->first();
            $user = $carrier->users()->first();
        }
        else if (!is_null($type->trans_figure_id)) {
            $driver = $type->driver()->first();
            $carrier = $driver->Carrier()->first();
            $user = $carrier->users()->first();
        }

        return $user->email;
    }

    public static function getUserMail()
    {
        return auth()->user()->email;
    }

    public static function getMails($carrierId = 0)
    {
        $mails = MailUtils::getClientMail();
        $type = UserVsTypes::where('is_deleted', 0)
                            ->where(($carrierId == 0 ? 'user_id' : 'carrier_id'), ($carrierId == 0 ? auth()->user()->id : $carrierId))
                            ->first();

        if ($type->is_principal) {
            array_push($mails, MailUtils::getUserMail());
        }
        else {
            array_push($mails, MailUtils::getCarrierMail());
            array_push($mails, MailUtils::getUserMail());
        }
        return $mails;
    }

    public static function getComercialName($carrierId = 0)
    {
        $type = UserVsTypes::where('is_deleted', 0)
                            ->where(($carrierId == 0 ? 'user_id' : 'carrier_id'), ($carrierId == 0 ? auth()->user()->id : $carrierId))
                            ->first();

        if (!is_null($type->carrier_id)) {
            $carrier = $type->carrier()->first();
        }
        else if (!is_null($type->trans_figure_id)) {
            $driver = $type->driver()->first();
            $carrier = $driver->Carrier()->first();
        }

        return $carrier->comercial_name;
    }
}