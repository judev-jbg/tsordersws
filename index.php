<?php

error_reporting(E_ALL);
ini_set('ignore_repeated_errors',TRUE);
ini_set('display_errors',FALSE);
ini_set('log_errors',TRUE);
ini_set('error_log',"C:\\xampp\\htdocs\\tsordersws\\traking.log");
error_log("====================================================");

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


//require_once "conf/config.php";
//require_once "libs/database.php";
//require_once "libs/app.php";

$route = new Route();
$route->handleRequest();

?>