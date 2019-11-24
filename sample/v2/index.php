<?php $loader = require './vendor/autoload.php';

use MrMe\Web\v2\Response;

// ------------------ Allow CORs ----------------------
if (env("ALLOW_CORS", false) == true)
{
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
}

// ----------------- SET date default timezone ------------------
//dd(env('TIMEZONE'));
date_default_timezone_set(env('TIMEZONE'));


// --------------- SET Error reporting -----------------------

ini_set('display_startup_errors', true);
ini_set('display_errors', true);

if (env('NOTICE_REPORTING', true) == 'false') error_reporting(E_ALL & ~E_NOTICE);


// --------------------- SHOW PHP INFO -------------------
if (env('PHPINFO', false) == 'true')  phpinfo();



// ------------------ ROUTING ------------------
try
{ 
    require_once "./route.php";
}
catch (Exception $ex)
{
    if (env("DEBUG_MODE", false) == 'true')
        dd("Error : ", $ex);
    throw new Exception($ex);
    //dd('Error :', $ex);
}

Response::reply_json([
    "success" => false,
    "data" => "Not found API",
], Response::NOT_FOUND);

echo "----- The Bottomless. You should never seen this! -----";
