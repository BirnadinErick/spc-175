<?php

namespace tinyfuse;

class Utils
{
    public static function logDebug(string $msg):void
    {
        $timestamp = date("Y-m-d H:i:s");
        fwrite(STDOUT, "DEBUG[$timestamp]: ". $msg);
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