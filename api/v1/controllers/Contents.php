<?php /** @noinspection PhpArrayKeyDoesNotMatchArrayShapeInspection */

namespace tinyfuse\controllers;

require_once MODELS . 'users.php';
require_once MODELS . 'comments.php';
require_once MODELS . 'contents.php';

use BumpCore\EditorPhp\Helpers;
use ContentsModel;

class Contents
{
    private ContentsModel $contents;

    public function __construct()
    {
        $this->contents = new ContentsModel();
    }

    public function read_content_html(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "GET") {
            http_response_code(400);
            echo("Our Engineers screwed up something, sorry. Please refresh the page");
            exit(1);
        }

        if (isset($_SERVER['HTTP_HX_CURRENT_URL'])) {
            $url = $_SERVER['HTTP_HX_CURRENT_URL'];
            $path = parse_url($url, PHP_URL_PATH);
            debug("path from HX-Header with url: $url and path: $path", __FILE__);
        } elseif (isset($_GET["path"])) {
            $path = $_GET["path"];
            debug("path from _GET with path: $path", __FILE__);
        } else {
            echo "NOT FOUND";
            http_response_code(404);
            exit(1);
        }

        $content = $this->contents->read_content("posts" . $path . ".html");
        if ($content === null) {
            debug("content not found" . var_export($content, true), __FILE__);
            echo Helpers::renderNative(VIEWS . '404.html', []);
            exit(1);
        }

        echo $content;
    }

}