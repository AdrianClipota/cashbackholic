<?php
/**
 * Created by PhpStorm.
 * User: cosmi
 * Date: 11-Oct-18
 * Time: 8:31 PM
 */

namespace App\Utils;


class Utils
{
    /**
     * @param $value
     * @author Popescu Ionut Cosmin
     **/
    public static function formatPrintr($value)
    {
        echo "<pre>";
        print_r($value);
        echo "</pre>";
    }

    public static function clean($value)
    {
        return preg_replace('/\n|\r|\n\r|\t|\r\n|\n\t/', "", $value);
    }
}