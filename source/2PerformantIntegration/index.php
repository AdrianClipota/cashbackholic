<?php
/**
 * Created by PhpStorm.
 * User: cosmi
 * Date: 08-Oct-18
 * Time: 8:20 PM
 * index
 */

require 'TwoPerformantOperations.php';

use \App\Integration\TwoPerformantOperations;
use App\Utils;

$tpOps = new TwoPerformantOperations(new \App\Config\Config(), new \Monolog\Logger(TwoPerformantOperations::class));
$debug = new Utils\Utils();

header('Content-Type: application/json');
$page = isset($_GET["page"]) === false ? 1 : $_GET["page"];
echo json_encode($tpOps->getAdvertisers($page), true);



