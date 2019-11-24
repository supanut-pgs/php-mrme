<?php
namespace MrMe\Web\v2;

class ResponseBase
{
    public const CONTINUE               = 100;
    public const SWITCHING_PROTOCOLS    = 101;
    public const OK                     = 200;
    public const CREATED                = 201;
    public const BAD_REQUEST            = 400;
    public const NOT_FOUND              = 404;
    public const NOT_IMPLEMENTED        = 501;
    public const NOT_ACCEPTABLE         = 406;
    public const UNAUTHORIZED           = 401;

    public static function reply_json($data, $status_code = self::OK)
    {
        header('Content-Type: application/json');
        http_response_code($status_code);
        echo json_encode($data);
        exit;
    }
}

class Response extends ResponseBase
{
    private static $_instance;

    public static function instance()
    {
        if (!Response::$_instance)
            Response::$_instance = new Response();
        return Response::$_instance;

    }

    public function success($data, $code = self::OK){parent::reply_json($data, $code);}
    public function error($data, $code = self::BAD_REQUEST){parent::reply_json($data, $code);}
    public function unauthorized($data, $code = self::UNAUTHORIZED){parent::reply_json($data, $code);}

}
//
//case 100: $text = 'Continue'; break;
//case 101: $text = 'Switching Protocols'; break;
//case 200: $text = 'OK'; break;
//case 201: $text = 'Created'; break;
//case 202: $text = 'Accepted'; break;
//case 203: $text = 'Non-Authoritative Information'; break;
//case 204: $text = 'No Content'; break;
//case 205: $text = 'Reset Content'; break;
//case 206: $text = 'Partial Content'; break;
//case 300: $text = 'Multiple Choices'; break;
//case 301: $text = 'Moved Permanently'; break;
//case 302: $text = 'Moved Temporarily'; break;
//case 303: $text = 'See Other'; break;
//case 304: $text = 'Not Modified'; break;
//case 305: $text = 'Use Proxy'; break;
//case 400: $text = 'Bad Request'; break;
//case 401: $text = 'Unauthorized'; break;
//case 402: $text = 'Payment Required'; break;
//case 403: $text = 'Forbidden'; break;
//case 404: $text = 'Not Found'; break;
//case 405: $text = 'Method Not Allowed'; break;
//case 406: $text = 'Not Acceptable'; break;
//case 407: $text = 'Proxy Authentication Required'; break;
//case 408: $text = 'Request Time-out'; break;
//case 409: $text = 'Conflict'; break;
//case 410: $text = 'Gone'; break;
//case 411: $text = 'Length Required'; break;
//case 412: $text = 'Precondition Failed'; break;
//case 413: $text = 'Request Entity Too Large'; break;
//case 414: $text = 'Request-URI Too Large'; break;
//case 415: $text = 'Unsupported Media Type'; break;
//case 500: $text = 'Internal Server Error'; break;
//case 501: $text = 'Not Implemented'; break;
//case 502: $text = 'Bad Gateway'; break;
//case 503: $text = 'Service Unavailable'; break;
//case 504: $text = 'Gateway Time-out'; break;
//case 505: $text = 'HTTP Version not supported';