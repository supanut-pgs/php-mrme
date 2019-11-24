<?php
namespace MrMe\Util;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger; 

class Logger 
{
    private static $__loggers; 
    
    private static function initLogger()
    {
        if (self::$__loggers) return self::__loggers;
        
        $config = getJsonConfig();
        $config = $config->logger;
        
        if ($config && count($config) > 0) self::$__loggers = [];

        foreach ($config as $c)
        {
            $logger = new MonoLogger($c->name);
            $output_file_path = $c->output;
            $output_file_path = str_replace(
                "%date%", 
                date('Ymd'),
                $output_file_path
            );

            $stream = new StreamHandler($output_file_path, self::toLoggerMode($c->level));

            if ($c->format)
            {
                $formatter = new LineFormatter($c->format);
                $stream->setFormatter($formatter);
            }

            $logger->pushHandler($stream);

            self::$__loggers[] = $logger; 
        }

        return self::$__loggers;
    }

    private static function toLoggerMode($text)
    {
        if (!$text) return;
        $text = strtolower($text);
        switch ($text)
        {
            case "debug"    : return MonoLogger::DEBUG;
            case "info"     : return MonoLogger::INFO;
            case "notice"   : return MonoLogger::NOTICE;
            case "warning"  : return MonoLogger::WARNING;
            case "error"    : return MonoLogger::ERROR;
            case "critical" : return MonoLogger::CRITICAL;
            case "alert"    : return MonoLogger::ALERT;
            case "emergency": return MonoLogger::EMERGENCY;
        }
    }

    public static function getDefault()
    {
        return self::$__loggers && count(self::$__loggers) 
        ? self::$__loggers[0] : self::initLogger();     
    }

    public static function get($name)
    {   
        if (!self::$__loggers || !count(self::$__loggers)) self::initLogger();
        
        if (!self::$__loggers) return null; 
        
        foreach (self::$__loggers as $logger)
        {
            if ($logger->getName() == $name) return $logger;
        }
        
        return null;
    }
    
}
?>
