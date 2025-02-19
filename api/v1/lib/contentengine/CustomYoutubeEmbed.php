<?php

namespace tinyfuse\lib\contentengine;

use BumpCore\EditorPhp\Blocks\Embed;
use BumpCore\EditorPhp\Helpers;

class CustomYoutubeEmbed extends Embed
{
    public function render(): string
    {
        debug(var_export($this->data, true), __FILE__);
        return Helpers::renderNative(VIEWS . 'editor-youtube-embed.php', ["height" => $this->data->get('height'), "src" => $this->data->get('embed')]);
    }
}