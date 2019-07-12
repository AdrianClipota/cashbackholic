<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', '12000');

require 'library/UpdateRetailers.php';
require 'library/UpdateCoupons.php';
require_once("inc/config.inc.php");
require_once("inc/functions.inc.php");

(new UpdateRetailers())->start();
sleep(120);
(new UpdateCoupons())->start();