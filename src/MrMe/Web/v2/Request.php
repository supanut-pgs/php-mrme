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

        if (count($_REQUEST > 0))
            $this->body = json_decode(json_encode($_REQUEST, true));

        if ($params)
            $this->params = json_decode(json_encode($params, true));

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
}