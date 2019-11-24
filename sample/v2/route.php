<?php
use Api\Controller\Test;
use MrMe\Web\v2\Request;
use MrMe\Web\v2\Response;
use MrMe\Web\v2\Route;

Route::map("/", \Api\Controller\Auth\LoginController::create());
Route::map("/user", \Api\Controller\UserController::create());
Route::map("/data", \Api\Controller\DataController::create());

Route::get("/test1/x", function(Request $request, Response $response) {
    //    return [
    //        "asdf" => "asdf",
    //    ];
    //    var_dump($request->params->id);
        $response->success(["success_xxx" => true]);
    }
);

Route::get("/test1/x", Test::call("getX"));
Route::map("/test2/x", \Api\Controller\TestController2::create());