<?php

namespace tinyfuse;

class Kernel
{
    public static function handle(Request $request, BaseState $state): Response
    {
        $path = $request->get_route();
        [$http_method, [$controller, $method]] = $state->route($path);

        // route sanitization
        if ($http_method === null) {
            return Response::forNotFound();
        }
        if ($http_method !== $request->get_http_method()) {
            Utils::logDebug($request->get_http_method());
            return Response::forNotAllowed();
        }

        return $controller->$method($request, $state);
    }

}