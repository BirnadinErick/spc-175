<?php

namespace tinyfuse;

class Request
{
    public function __construct(
        public readonly array      $get_params,
        public readonly array      $post_params,
        public readonly array|null $session,
        public readonly array      $server

    )
    {
    }

    public static function fromGlobals(): static
    {
        return new self($_GET, $_POST, $_SESSION, $_SERVER);
    }

    public function get_route(): string|null
    {
        return array_key_exists("path", $this->get_params) ? $this->get_params['path'] : null;
    }

    // gets the current http method, if not able to determine, defaults to GET
    public function get_http_method():string
    {
        return array_key_exists("REQUEST_METHOD", $this->server) ? $this->server["REQUEST_METHOD"]:"GET";
    }

}