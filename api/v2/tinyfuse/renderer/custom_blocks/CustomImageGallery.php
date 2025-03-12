<?php

namespace tinyfuse\renderer\custom_blocks;

use BumpCore\EditorPhp\Block\Block;
use BumpCore\EditorPhp\Helpers;

class CustomImageGallery extends Block
{
    public function rules(): array
    {
        return [
            "urls" => "array"
        ];
    }

    public function render(): string
    {
        return Helpers::renderNative(VIEWS . 'editor-img-gallery.php', ["imgs" => $this->data->get('urls')]);
    }
}