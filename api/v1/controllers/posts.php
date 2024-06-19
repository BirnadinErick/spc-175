<?php

use BumpCore\EditorPhp\EditorPhp;

require_once MODELS . "users.php";
require_once MODELS . "contents.php";

function save_post()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(400);
        exit(1);
    }

    session_start();
    if (!isset($_SESSION["email"])) {
        debug("annonymous post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $users = new UsersModel();
    $contents = new ContentsModel();

    if (!$users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
        debug("unauthorized post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $path = $_POST["path"];
    $data = $_POST["data"];
    $uid = uniqid('spc_media_unit_', true);
    $user_id = $users->get_user_id($_SESSION["email"]);
    $compressed_data = bzcompress($data, 9);

    try {
        $ok = $contents->update_content($path, $uid, $user_id, $compressed_data);

        if ($ok != 0) {
            // TODO: specify why
            throw new Exception("content update failed");
        }
    } catch (Exception $ex) {
        session_write_close();
        debug("writing failed", __FILE__);
        http_response_code(500);
        exit(1);
    }

    session_write_close();
    http_response_code(204);
    exit(0);
}

function read_post()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(400);
        exit(1);
    }

    session_start();
    if (!isset($_SESSION["email"])) {
        http_response_code(401);
        exit(1);
    }

    $users = new UsersModel();
    if (!$users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
        http_response_code(401);
        exit(1);
    }

    $contents = new ContentsModel();
    $path = $_GET["path"];

    $content = $contents->read_content($path);
    debug("request for the contents of path: $path ", __FILE__);

    $data = bzdecompress($content["data"]);

    header('Content-Type: application/json; charset=utf-8');
    echo $data;

    session_write_close();
    exit(0);
}

function read_post_html()
{
    if ($_SERVER["REQUEST_METHOD"] !== "GET") {
        http_response_code(400);
        echo("Our Engineers screwed up something, sorry. Please refresh the page");
        exit(1);
    }

    if (isset($_SERVER['HTTP_HX_CURRENT_URL'])) {
        $url = $_SERVER['HTTP_HX_CURRENT_URL'];
        $path = parse_url($url, PHP_URL_PATH);
    } elseif (isset($_GET["path"])) {
        $path = $_GET["path"];
    } else {
        echo "NOT FOUND";
        http_response_code(404);
        exit(1);
    }

    $contents = new ContentsModel();
    $content = $contents->read_content($path);
    $json = bzdecompress($content["data"]);

    $render = EditorPhp::make($json)->render();

    echo $render;
    http_response_code(200);
    exit(0);
}

function create_post()
{
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(400);
        echo("Our Engineers screwed up something, sorry. Please refresh the page");
        exit(1);
    }


    session_start();
    if (!isset($_SESSION["email"])) {
        debug("annonymous post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $users = new UsersModel();
    $contents = new ContentsModel();

    if (!$users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
        debug("unauthorized post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $path = $_POST["path"];
    $data = $_POST["data"];
    $uid = uniqid('spc_media_unit_', true);
    $user_id = $users->get_user_id($_SESSION["email"]);
    $compressed_data = bzcompress($data, 9);

    try {
        $contents->write_content($path, $uid, $user_id, $compressed_data);
    } catch (Exception $ex) {
        session_write_close();
        debug("writing failed", __FILE__);
        http_response_code(500);
        exit(1);
    }

    session_write_close();
    http_response_code(201);
    exit(0);
}