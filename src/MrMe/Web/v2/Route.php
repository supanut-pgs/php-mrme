<?php
namespace MrMe\Web\v2;

class RouteBase
{
    public static function map($path, Controller $controller)
    {
        $query = explode('=', $_SERVER['REDIRECT_QUERY_STRING'])[1];
        $path = ltrim($path, '/');
        $subfix_path = ltrim($query, $path);
        $subfix_path = explode('/', $subfix_path);
        $func = array_shift($subfix_path);

        $params = implode('/', $subfix_path);
        $match_path = rtrim($query, "/$func/$params");
        $params = ltrim($params, '/');
//
//        var_dump("Query = " . $query);
//        var_dump("Path = " . $path);
//        var_dump("Function = " . $func);
//        var_dump("Match Path = " . $match_path);
//        var_dump("Params = " . $params);
//        var_dump("Matching = " . rtrim($query, $params));
//
//        var_dump("Params Object", RouteBase::convertTextToParams($params));
        if ($match_path == $path)
        {
            if (method_exists($controller, $func))
            {
                $ret_val = $controller->{$func}(Request::instance($params), Response::instance());
                if (gettype($ret_val) == 'array')
                    Response::reply_json($ret_val);

            }

        }

        Response::reply_json([
            "success" => false,
            "data" => "Not found API $path",
        ], Response::NOT_FOUND);
    }

    public static function route($path, $obj)
    {
        $query = explode('=', $_SERVER['REDIRECT_QUERY_STRING'])[1];
        $path = ltrim($path, '/');

        $params = ltrim($query, $path);
        $params = ltrim($params, '/');

        $match_path = rtrim($query, $params);

//        var_dump("Query = " . $query);
//        var_dump("Path = " . $path);
//        var_dump("Params = " . $params);
//        var_dump("Matching = " . rtrim($query, $params));

//        var_dump("Params Object", RouteBase::convertTextToParams($params));
        if ($match_path == $path)
        {

            if ($obj)
            {
                $obj = $obj(Request::instance($params));

                if (gettype($obj) == 'array')
                    Response::reply_json($obj);
            }

            Response::reply_json([
                "success" => false,
                "data" => "Not Implemented $path",
            ], Response::NOT_IMPLEMENTED);
        }
        else
        {
            Response::reply_json([
                "success" => false,
                "data" => "Not found API $path",
            ], Response::NOT_FOUND);
        }
       
    }
}

class Route extends RouteBase
{
    public static function post($path, $obj)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') exit(0);

       parent::route($path, $obj);
    }

    public static function get($path, $obj)
    {
        if ($_SERVER['REQUEST_METHOD'] != "GET") exit(0);

        parent::route($path, $obj);

    }
}
