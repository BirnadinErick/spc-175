<?php

namespace tinyfuse;

class Response
{
    public function __construct(
        private readonly string $content,
        private readonly string $type = 'text/plain;',
        private STATUS_CODES    $code = STATUS_CODES::OK
    )
    {
    }

    public static function forNotFound():static
    {
        $message = 'Page not found.';
        return new self($message, code: STATUS_CODES::NOT_FOUND);
    }

    public static function forNotAllowed(): static
    {
        $message = 'Method not allowed';
        return new self($message, code: STATUS_CODES::NOT_ALLOWED);
    }

    public function setCode(STATUS_CODES $code): void
    {
        $this->code = $code;
    }

    public function sendHeaders(): void
    {
        header("Content-Type: $this->type");
        http_response_code($this->code->value);
    }

    public function sendContent(): void
    {
        echo $this->content;
    }

    // mostly this function is used, but if
    // anything should be sent between headers and content
    // then use the other to.
    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();
    }
}