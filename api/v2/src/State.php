<?php

namespace App;

use Tinyfuse\Kernel\BaseState;

class State extends BaseState
{
    public array $routes;

    public function __construct(array|string $ENV = 'production')
    {
        parent::__construct($ENV);
        $this->routes = [];
    }

    public function addRoute(string $method, string $path, array $handler): bool
    {
        if (count($handler) !== 2) {
            return false;
        }

        $this->routes[] = [$path => [$method, $handler]];
        return true;
    }

    public function getHandlers(string $path):array|null
    {
        return array_key_exists($path, $this->routes) ? $this->routes[$path] : null;
    }

}