<?php

namespace tinyfuse\lib\contentengine;

use BumpCore\EditorPhp\EditorPhp;

require_once APP."lib/contentengine/CustomDelimiter.php";
require_once APP."lib/contentengine/CustomImageGallery.php";
require_once APP."lib/contentengine/CustomSimpleImage.php";
require_once APP."lib/contentengine/CustomYoutubeEmbed.php";
class ContentEngine
{
    public function render(string $raw): string
    {
        EditorPhp::register([
            "imageGallery" => CustomImageGallery::class,
            "image" => CustomSimpleImage::class,
            "delimiter" => CustomDelimiter::class,
            "embed" => CustomYoutubeEmbed::class,
        ]);
        return EditorPhp::make($raw)->render();
    }

}