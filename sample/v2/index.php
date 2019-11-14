<?php $loader = require './vendor/autoload.php';

use Api\Controller\Test;
use MrMe\Web\v2\Request;
use MrMe\Web\v2\Response;
use MrMe\Web\v2\Route;


// ------------------ Allow CORs ----------------------
if (isset($_SERVER['HTTP_ORIGIN'])) 
{
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Max-Age: 86400"); // cache for 1 day 
}

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') 
{
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

// ----------------- SET date default timezone ------------------
date_default_timezone_get("Asia/Bangkok");


// --------------- SET Error reporting -----------------------
error_reporting(E_ALL | ~E_NOTICE);
ini_set('display_startup_errors', true);
ini_set('display_errors', true);


//Route::get("/test1/x", function(Request $request, Response $response) {
////    return [
////        "asdf" => "asdf",
////    ];
////    var_dump($request->params->id);
//    $response->success(["success" => true]);
//});

try
{
//    Route::get("/test1/x", Test::call("getX"));
    Route::map("/test2/x", \Api\Controller\TestController2::create());
}
catch (Exception $ex)
{
    var_dump("ds");
}

echo "----- The Bottomless. You should never seen this! -----";
