<?php

namespace tinyfuse;

class Request
{
    public function __construct(
        private readonly array $get_params,
        private readonly array $post_params,
        private readonly array $server
    )
    {
    }

    public static function fromGlobals(): static
    {
        return new self($_GET, $_POST, $_SERVER);
    }

    public function get_route(): string|null
    {
        return array_key_exists("path", $this->get_params) ? $this->get_params['path'] : null;
    }

    // gets the current http method, if not able to determine, defaults to GET
    public function get_http_method(): string
    {
        return array_key_exists("REQUEST_METHOD", $this->server) ? $this->server["REQUEST_METHOD"] : "GET";
    }

    public function get_post_params(): array
    {
        return $this->post_params;
    }

    public function get_get_param($query_key): null|string
    {
        return array_key_exists($query_key, $this->get_params) === true ? $this->get_params[$query_key] : null;
    }

}