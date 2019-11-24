<?php
namespace MrMe\Messages\Web;

use MrMe\Web\v2\Response;

class ApiResponseMessage
{
    public static function notFound($desc)
    {
        Response::instance()->notFound([
            "success" => false,
            "data" => $desc,
        ]);
    }
}