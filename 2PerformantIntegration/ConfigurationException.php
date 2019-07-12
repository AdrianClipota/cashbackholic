<?php
/**
 * Created by PhpStorm.
 * User: cosmi
 * Date: 17-Oct-18
 * Time: 7:26 AM
 */

namespace App\Exception;


use Throwable;

class ConfigurationException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return __CLASS__ . ": [{$this->code}] : {$this->message}\n";
    }
}