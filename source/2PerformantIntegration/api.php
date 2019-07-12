<?php
/**
 * Created by PhpStorm.
 * User: cosmi
 * Date: 08-Oct-18
 * Time: 8:20 PM
 * index
 */
error_reporting(E_ALL & ~E_NOTICE);
require 'TwoPerformantOperations.php';

use \App\Integration\TwoPerformantOperations;
use App\Utils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$tpOps = new TwoPerformantOperations(new \App\Config\Config(), new \Monolog\Logger(TwoPerformantOperations::class));
$debug = new Utils\Utils();
$request = Request::createFromGlobals();
$response = new Response();

$page = ($request->query->get('page') === null) ? 1 : $request->query->get('page');
$response->setContent(json_encode($tpOps->getAdvertisers($page)));
$response->headers->set('Content-Type', 'application/json');
$response->send();


