<?php

namespace tinyfuse;

class Kernel
{
    public static function handle(Request $request, BaseState $state): Response
    {
        $path = $request->get_route();
        if ($path === null) {
            return Response::forNotFound();
        }

        [$http_method, [$controller, $method]] = $state->route($path);
        if ($http_method !== $request->get_http_method()) {
            return Response::forNotAllowed();
        }

        $response = $controller->$method($request, $state);

        if ($state->LOG_DEBUG_VARS === true) {
            dump($request);
            dump($state);
            dump($response);
        }
        return $response;
    }

}