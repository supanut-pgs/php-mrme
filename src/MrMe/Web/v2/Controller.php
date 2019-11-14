<?php
namespace MrMe\Web\v2;

use Api\Controller\Test;

class Controller
{
    public static function create()
    {
        $class = get_called_class();
        $class = new $class;

        return $class;
    }

    public static function call($func)
    {
        $class = get_called_class();
        $class = new $class;

        return function(Request $request, Response $response) use ($class, $func) {
            if (gettype($val = $class->{$func}($request, $response)) == 'array')
                return $val;
        };

    }
}