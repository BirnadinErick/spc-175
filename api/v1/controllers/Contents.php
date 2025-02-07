<?php /** @noinspection PhpArrayKeyDoesNotMatchArrayShapeInspection */

namespace tinyfuse\controllers;

require_once MODELS . 'UsersModel.php';
require_once MODELS . 'comments.php';
require_once MODELS . 'contents.php';
require_once MODELS . 'ContentsV2Model.php';

use BumpCore\EditorPhp\Helpers;
use ContentsModel;
use tinyfuse\lib\contentengine\ContentEngine;
use tinyfuse\models\ContentsV2Model;
use tinyfuse\models\UsersModel;

class Contents
{
    private ContentsModel $contents;
    private ContentsV2Model $contents_v2;
    private UsersModel $users;
    private ContentEngine $engine;

    public function __construct()
    {
        $this->contents = new ContentsModel();
        $this->contents_v2 = new ContentsV2Model();
        $this->users = new UsersModel();
        $this->engine = new ContentEngine();
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

        $content = $this->contents_v2->get_rendered_content($path);
        if ($content === null) {
            debug("content not found" . var_export($content, true), __FILE__);
            echo Helpers::renderNative(VIEWS . '404.html', []);
            exit(1);
        }

        echo $content;
    }

    public function read_content_raw(): void
    {
        ensure_request_method("GET");

        session_start();
        if (!isset($_SESSION["email"])) {
            debug("annonymous content raw read attempt", __FILE__);
            http_response_code(401);
            exit(1);
        }


        if (!$this->users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
            debug("unauthorized post save attempt", __FILE__);
            http_response_code(HTTP_STATUS_UNAUTHORIZED);
            exit(1);
        }

        if (!isset($_GET['path'])) {
            debug("read_raw: no path in request", __FILE__);
            http_response_code(HTTP_STATUS_BAD_REQUEST);
            exit(1);
        }

        $path = $_GET['path'];
        debug($path, __FILE__);
        $content_raw = $this->contents_v2->get_raw($path);
        if ($content_raw === null) {
            debug("read_raw: $path not found in db", __FILE__);
            http_response_code(HTTP_STATUS_NOT_FOUND);
            exit(1);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo $content_raw;

        session_write_close();
        exit(0);
    }

    public function delete_content(): void
    {
        ensure_request_method("POST");

        session_start();
        if (!isset($_SESSION["email"])) {
            debug("annonymous post save attempt", __FILE__);
            http_response_code(401);
            exit(1);
        }

        if (!$this->users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
            debug("unauthorized post save attempt", __FILE__);
            http_response_code(HTTP_STATUS_UNAUTHORIZED);
            exit(1);
        }

        if (!isset($_GET['path'])) {
            http_response_code(HTTP_STATUS_BAD_REQUEST);
            exit(1);
        }

        $path = $_GET['path'];
        if ($this->contents_v2->delete($path)) {
            echo "Deleted, please refresh the page";
            exit(0);
        } else {
            echo "Failed!";
            exit(1);
        }
    }

    public function update_content(): void
    {
        ensure_request_method("POST");

        session_start();
        if (!isset($_SESSION["email"])) {
            debug("annonymous post save attempt", __FILE__);
            http_response_code(401);
            exit(1);
        }

        if (!$this->users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
            debug("unauthorized post save attempt", __FILE__);
            http_response_code(HTTP_STATUS_UNAUTHORIZED);
            exit(1);
        }
        $email = $_SESSION['email'];
        $user_id = $this->users->get_user_id($email);

        if (!isset($_POST['path']) || !isset($_POST['data'])) {
            debug("read_raw: no path in request", __FILE__);
            http_response_code(HTTP_STATUS_BAD_REQUEST);
            exit(1);
        }

        $data_raw = $_POST['data'];
        $path = $_POST['path'];
        $ok = $this->contents_v2->update($path, $user_id, $data_raw, $this->engine->render($data_raw));
        if (!$ok) {
            debug("updateing content: $path failed!", __FILE__);
            http_response_code(HTTP_STATUS_SERVER_ERROR);
            exit(1);
        }

        session_write_close();
        http_response_code(HTTP_STATUS_SAVED);
        exit(0);
    }

    public function migrate(): void
    {
        if ($this->contents_v2->migrate_metadata()){
            echo "done!";
        }else {
            echo "failed";
        }
    }
}