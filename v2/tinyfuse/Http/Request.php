<?php

namespace Tinyfuse\Http;

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

}