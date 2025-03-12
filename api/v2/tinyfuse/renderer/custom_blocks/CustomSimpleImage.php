<?php

namespace tinyfuse\renderer\custom_blocks;

use BumpCore\EditorPhp\Blocks\Image;
use BumpCore\EditorPhp\Helpers;

class CustomSimpleImage extends Image
{
    public function rules(): array
    {
        return [
            "url" => "string",
            "caption" => "string"
        ];
    }

    public function render(): string
    {
        $data = $this->data;
        return Helpers::renderNative(VIEWS . 'editor-img.php', ["url" => $data('url')]);
    }
}