<?php

function env($key, $default = "")
{
    return findWithKey(".env", $key, $default);
}

function config($key, $default = "")
{
    return findWithKey(".config", $key, $default);
}

function findWithKey($file, $key, $default = "")
{
    $contents = file_get_contents($file);
    //$contents = explode(PHP_EOL, trim($contents));
    $contents = preg_split('/\r\n|\r|\n/', $contents);
    //dd($contents);
    foreach ($contents as $c)
    {
        if (!$c) continue;
        $line = explode('=', $c);
   
        if ($line[0]==$key) return $line[1];
    }
    return $default;
}

function getJsonConfig()
{
    $json = file_get_contents('config.json');
    $config = json_decode($json);
    return $config; 
}