<?php

namespace Tinyfuse\Kernel;

use Tinyfuse\Http\Request;
use Tinyfuse\Http\Response;

class Kernel
{
    public static function handle(Request $request, BaseState $state): Response
    {
        dump($request);
        $response = new Response('Hello');
        dump($state);
        return $response;
    }

}