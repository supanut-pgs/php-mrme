<?php
namespace Api\Controller;

use MrMe\Web\v2\Controller;
use MrMe\Web\v2\Request;
use MrMe\Web\v2\Response;

class Test extends Controller
{
    public function getX(Request $request, Response $response)
    {
//        var_dump("T");

//        $response->success([
//            "success" => 'ti'
//        ]);

//        return ['T' => 's'];
        $response->success(["success" => true]);
    }
}