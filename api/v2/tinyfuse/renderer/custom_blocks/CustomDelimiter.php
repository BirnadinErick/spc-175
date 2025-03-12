<?php

namespace tinyfuse\renderer\custom_blocks;

use BumpCore\EditorPhp\Blocks\Delimiter;

class CustomDelimiter extends Delimiter
{
    public function render(): string
    {
        return "<hr />";
    }
}