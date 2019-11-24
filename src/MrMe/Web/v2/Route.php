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
        array_shift($subfix_path);
       
        $func = count($subfix_path) ? $subfix_path[0] : $query;
       
        $params = implode('/', $subfix_path);
        $match_path = ($tmp = rtrim($query, "/$func/$params")) ? $tmp : $query;
        $params = ltrim($params, '/');

        // var_dump("Query = " . $query);
        // var_dump("Path = " . $path);
        // // var_dump("Subfix_path = " . $subfix_path);
        // var_dump("Function = " . $func);
        // var_dump("Match Path = " . $match_path);
        // var_dump("Params = " . $params);
        // var_dump("Matching = " . rtrim($query, $params));
        // var_dump("Controller = ", $controller);

        // var_dump("Params Object", $params);
      
        if (!$path || $match_path == $path)
        {
            if (method_exists($controller, $func))
            {
                $request = Request::instance($params);
                $response = Response::instance();

                $controller->request = $request;
                $controller->response = $response; 

                $ret_val = $controller->{$func}($request, $response);
                if (gettype($ret_val) == 'array')
                    Response::reply_json($ret_val);

            }

        }
    }

    public static function route($path, $obj)
    {
        $query = explode('=', $_SERVER['REDIRECT_QUERY_STRING'])[1];
        $path = ltrim($path, '/');

        $params = ltrim($query, $path);
        $params = ltrim($params, '/');

        $match_path = rtrim($query, $params);

        // var_dump("Query = " . $query);
        // var_dump("Path = " . $path);
        // var_dump("Params = " . $params);
        // var_dump("Matching = " . rtrim($query, $params));

        // var_dump("Params Object", RouteBase::convertTextToParams($params));
        if ($match_path == $path)
        {

            if ($obj)
            {
                $obj = $obj(Request::instance($params), Response::instance());

                if (gettype($obj) == 'array')
                    Response::reply_json($obj);
            }
        }
    }
}

class Route extends RouteBase
{
    public static function post($path, $obj)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') return;

        parent::route($path, $obj);
    }

    public static function get($path, $obj)
    {
        if ($_SERVER['REQUEST_METHOD'] != "GET") return;

        parent::route($path, $obj);

    }
}
