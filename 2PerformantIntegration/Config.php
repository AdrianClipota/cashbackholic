<?php
/**
 * Created by PhpStorm.
 * User: cosmi
 * Date: 08-Oct-18
 * Time: 9:08 PM
 */

namespace App\Config;
use Dotenv\Dotenv;

require 'vendor/autoload.php';

/**
 * Class Config
 * @package App\Config
 * Holds the information for connection to 2Performant API
 */
class Config
{

    private $email;
    private $password;

    public function __construct()
    {
        $dotenv = new Dotenv(__DIR__);
        $dotenv->load();
        $this->setEmail(getenv("EMAIL"));
        $this->setPassword(getenv("PASSWORD"));
    }


    private function setEmail($_email)
    {
        $this->email = $_email;
    }

    private function setPassword($_password)
    {
        $this->password = $_password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
