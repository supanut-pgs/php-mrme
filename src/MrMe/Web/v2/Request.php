<?php
namespace MrMe\Web\v2;

class RequestBase
{
    public $addr;
    public $params;
    public $body;
    public $json;
    public $file;
    

//    public function setParams($params)
//    {
//        $this->params = json_decode(json_encode($params, true));;
//    }

    public function __construct($params = null)
    {
        $this->addr = $_SERVER['REMOTE_ADDR'];
        $this->json = json_decode(file_get_contents('php://input'));

        if (count($_REQUEST))
            $this->body = json_decode(json_encode($_REQUEST, true));

        if ($params)
            $this->params = json_decode(json_encode($params, true));

        foreach ($_REQUEST as $key => $value) $this->{$key} = $value;

        if ($params)
            foreach ($params as $key => $value) $this->{$key} = $value;

    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        unset($this->addr);
        unset($this->params);
        unset($this->body);
        unset($this->json);
        unset($this->file);
    }

}

class Request extends RequestBase
{
    private static $_instance;
    public static function instance($params = null)
    {
//        var_dump(gettype($params));
        if ($params && gettype($params) == 'string')
        {
            $params = self::parseParamsFromText($params);
        }
        if (!Request::$_instance)
            Request::$_instance = new Request($params);
        return Request::$_instance;
    }

    private static function parseParamsFromText($text)
    {
        $text = explode("/", $text);
        $params = [];
        foreach ($text as $index => $value)
        {
            if ($index%2 == 0) continue;

            $params[] = [$text[$index-1] => $value];
        }

        return count($params) ? $params[0] : $params;
    }

    public function __construct($params = null)
    {
        parent::__construct($params);
    }

    public static function acceptMethod(... $methods)
    {
        if ($methods == "*") return true; 

        $method = $_SERVER['REQUEST_METHOD'];
        foreach ($methods as $m)
        {
            if ($m == $method)  return true; 
            if ($m == "*")      return 0;
        }

        Response::reply_json(
            [
                "success"   => false,
                "data"      => "This method not accepted from this API"
            ],
            Response::NOT_ACCEPTABLE
        );
    }

    // $request->validate([
    //         "username" => "body|required",
    //         "password" => "params|required",
    // ])
    private function responseRequired($message)
    {
        Response::reply_json(
            [
                "success"   => false,
                "data"      => $message
            ],
            Response::BAD_REQUEST
        );
    }

    public function validate($objArray)
    {
        foreach ($objArray as $key => $validation)
        {      
            $validate_attr = $key; // username 
            $val_exp = explode("|", $validation); //(body, required)
            $type = $val_exp[0];
            $val = count($val_exp) == 1 ? "required" : $val_exp[1]; 
            
            if ($type == "body")
            {
                if ($val == "required")
                    if(empty($this->body->{$validate_attr})) 
                        $this->responseRequired("Required `{$key}`");
                
            }

            if ($type == "params")
            {
                if ($val == "required")
                    if(empty($this->body->{$validate_attr})) return false;
            }
        }
    }
}