<?php

error_reporting(E_ALL);
ini_set('ignore_repeated_errors', TRUE);
ini_set('display_errors', FALSE);
ini_set('log_errors', TRUE);
ini_set('error_log', "C:\\xampp\\htdocs\\tsordersws\\traking.log");
error_log("====================================================");

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once "config/apikey.php";
require_once "config/routes.php";
require_once "config/database.php";
require_once "libs/Database.php";
require_once "models/OrderModel.php";
require_once "controllers/OrderController.php";
require_once "routes/Route.php";

$route = new Route();
$route->handleRequest();
