<?php

namespace tinyfuse;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class Utils
{
    public static function logDebug(string $msg):void
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler('debug.log', Level::Debug));
        $timestamp = date("Y-m-d H:i:s");
        $log_data =  "DEBUG[$timestamp]: ". $msg;
        error_log($log_data);
        $log->debug($log_data);
    }

    public static function get_time_full():string
    {
       return date('Y-m-d H:i:s');
    }

    public static function logInfo(string $msg):void
    {
        $timestamp = date("Y-m-d H:i:s");
        error_log("INFO[$timestamp]: ".$msg);
    }

    public static function logRequest(): void
    {
        $timestamp = date("Y-m-d H:i:s");
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $ip = $_SERVER['REMOTE_ADDR'];

        error_log("NEW_REQ[$timestamp]: $ip - $method $uri\n");
    }
}