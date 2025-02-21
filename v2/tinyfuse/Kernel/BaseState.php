<?php

namespace Tinyfuse\Kernel;

class BaseState
{
    public LOG_LEVEL $LOG_LEVEL;
    public bool $DEBUG;

    public function __construct(
        public readonly array $global_env
    )
    {
        if ($this->global_env['ENV'] === 'development') {
            $this->DEBUG = true;
            $this->LOG_LEVEL = LOG_LEVEL::DEBUG;
        } else {
            $this->DEBUG = false;
            $this->LOG_LEVEL = LOG_LEVEL::INFO;
        }

    }

}