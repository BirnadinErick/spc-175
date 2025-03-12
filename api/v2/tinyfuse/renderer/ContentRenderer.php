<?php

namespace tinyfuse\renderer;

use BumpCore\EditorPhp\EditorPhp;

trait ContentRenderer
{
    protected function content_render(string $raw): string
    {
        EditorPhp::register([
            "imageGallery" => custom_blocks\CustomImageGallery::class,
            "image" => custom_blocks\CustomSimpleImage::class,
            "delimiter" => custom_blocks\CustomDelimiter::class,
            "embed" => custom_blocks\CustomYoutubeEmbed::class,
        ]);

        return EditorPhp::make($raw)->render();
    }
}