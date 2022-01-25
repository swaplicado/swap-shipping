<?php namespace App\Utils;

use Carbon\Carbon;

class Configuration {

    public static function getConfigurations()
    {
        // Read File
        $jsonString = file_get_contents(base_path('shipconfig.json'));
        $data = json_decode($jsonString);

        return $data;
    }

    public static function setConfiguration($key, $value)
    {
        // Read File
        $jsonString = file_get_contents(base_path('shipconfig.json'));
        $data = json_decode($jsonString, true);

        // Update Key
        $data[$key] = $value;

        // Write File
        $newJsonString = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(base_path('shipconfig.json'), stripslashes($newJsonString));
    }
}