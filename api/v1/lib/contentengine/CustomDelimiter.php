<?php

namespace tinyfuse\lib\contentengine;

use BumpCore\EditorPhp\Blocks\Delimiter;

class CustomDelimiter extends Delimiter
{
    public function render(): string
    {
        return "<hr />";
    }
}