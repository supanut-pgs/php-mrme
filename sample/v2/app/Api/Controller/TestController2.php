<?php
namespace Api\Controller;

use MrMe\Web\v2\Controller;
use MrMe\Web\v2\Request;
use MrMe\Web\v2\Response;

class TestController2 extends Controller
{
    public function getX(Request $request, Response $response)
    {
        $response->error(["success" => true]);
    }

    public function getY()
    {
        return [
            "success" => "Y",
        ];
    }
}