<?php

namespace tinyfuse;

class BaseState
{
    public LOG_LEVEL $LOG_LEVEL;
    public bool $LOG_DEBUG_VARS;
    public bool $DEBUG;
    public string $VIEWS;

    protected array $routes;

    public function __construct(
        private readonly array $global_env
    )
    {
        $this->routes = [];
        $this->VIEWS = ROOT . '/app/views/';

        if ($this->global_env['ENV'] === 'development') {
            $this->DEBUG = true;
            $this->LOG_LEVEL = LOG_LEVEL::DEBUG;
        } else {
            $this->DEBUG = false;
            $this->LOG_LEVEL = LOG_LEVEL::INFO;
        }

        if ($this->global_env['LOG_DEBUG_VARS'] === '1') {
            $this->LOG_DEBUG_VARS = true;
        } else {
            $this->LOG_DEBUG_VARS = false;
        }

    }

    public function set_view_root(string $view_root):void
    {
        $this->VIEWS = $view_root;
    }

    public static function default(): static
    {
        return new self($_ENV);
    }

    /**
     * @param string $path /endpoint
     * @return array|null [controller::class, method_name] | null (404)
     */
    public function route(string $path): array|null
    {
        return array_key_exists($path, $this->routes) ? $this->routes[$path] : null;
    }

    /**
     * @param string $method GET/POST
     * @param string $path /endpoint
     * @param array $handler [controller::class, method_name]
     * @return bool whether the endpoint addition is success or not
     */
    public function addRoute(string $method, string $path, array $handler): bool
    {
        if (count($handler) !== 2) {
            return false;
        }

        $this->routes[$path] = [$method, $handler];
        return true;
    }

    public function get_env(string $key): string|null
    {
        return array_key_exists($key, $this->global_env) ? $this->global_env[$key] : null;
    }
}