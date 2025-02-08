<?php

namespace tinyfuse\controllers;

require_once MODELS . 'UsersModel.php';
require_once MODELS . 'BlogsModel.php';
require_once MODELS . 'comments.php';
require_once MODELS . 'contents.php';
require_once MODELS . 'ContentsV2Model.php';

use BumpCore\EditorPhp\Helpers;
use tinyfuse\lib\contentengine\ContentEngine;
use tinyfuse\lib\Settings;
use tinyfuse\models\BlogsModel;
use tinyfuse\models\UsersModel;


class Blogs
{
    private UsersModel $users;
    private BlogsModel $blogs;
    private ContentEngine $engine;

    public function __construct()
    {
        $this->users = new UsersModel();
        $this->blogs = new BlogsModel();
        $this->engine = new ContentEngine();
    }

    public function create_blog(): void
    {
        ensure_request_method("POST");

        session_start();
        if (!isset($_SESSION["email"])) {
            debug("annonymous content raw read attempt", __FILE__);
            http_response_code(401);
            exit(1);
        }
        $email = $_SESSION['email'];

        if (!$this->users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
            debug("unauthorized post save attempt", __FILE__);
            http_response_code(HTTP_STATUS_UNAUTHORIZED);
            exit(1);
        }

        $requiredFields = ['title', 'tags', 'cover', 'data'];
        foreach ($requiredFields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                http_response_code(400);
                exit(1);
            }
        }

        $title = trim($_POST['title']);
        $tags = trim($_POST['tags']);
        $cover = trim($_POST['cover']);
        $data = $_POST['data'];
        $user_id = $this->users->get_user_id($email);

        $slug = $this->blogs->new_blog($title, $data, $tags, $cover, $user_id);
        if (!$slug) {
            http_response_code(HTTP_STATUS_SERVER_ERROR);
            echo "Failed to create new blog";
            exit(1);
        }

        http_response_code(HTTP_STATUS_CREATED);
        echo "/blogs/entry?p=" . $slug;

        session_write_close();
        exit(0);
    }

    public function editable_blogs(): void
    {
        ensure_request_method("GET");

        session_start();
        if (!isset($_SESSION["email"])) {
            debug("annonymous content read attempt", __FILE__);
            http_response_code(401);
            exit(1);
        }

        if (!$this->users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
            debug("unauthorized post save attempt", __FILE__);
            http_response_code(HTTP_STATUS_UNAUTHORIZED);
            exit(1);
        }

        $cs = $this->blogs->get_editable_contents();
        echo Helpers::renderNative(VIEWS . 'available-blogs.php', [
            "cs" => $cs
        ]);

        session_write_close();
        exit(0);
    }

    public function delete_blog(): void
    {
        ensure_request_method("POST");

        session_start();
        if (!isset($_SESSION["email"])) {
            debug("annonymous blog delete attempt", __FILE__);
            http_response_code(401);
            exit(1);
        }

        if (!$this->users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
            debug("unauthorized blog delete attempt", __FILE__);
            http_response_code(HTTP_STATUS_UNAUTHORIZED);
            exit(1);
        }
        session_write_close();

        if (!isset($_GET['slug'])) {
            http_response_code(HTTP_STATUS_BAD_REQUEST);
            exit(1);
        }

        $slug = $_GET['slug'];
        if ($this->blogs->delete($slug)) {
            echo "Deleted, please refresh the page";
            exit(0);
        } else {
            echo "Failed!";
            exit(1);
        }
    }

    public function update_blog(): void
    {
        ensure_request_method("POST");

        session_start();
        if (!isset($_SESSION["email"])) {
            debug("annonymous blog delete attempt", __FILE__);
            http_response_code(401);
            exit(1);
        }

        if (!$this->users->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])) {
            debug("unauthorized blog delete attempt", __FILE__);
            http_response_code(HTTP_STATUS_UNAUTHORIZED);
            exit(1);
        }
        $email = $_SESSION['email'];
        $user_id = $this->users->get_user_id($email);

        if (!isset($_POST['slug']) || !isset($_POST['data'])) {
            debug("read_raw: bad request", __FILE__);
            http_response_code(HTTP_STATUS_BAD_REQUEST);
            exit(1);
        }

        $data_raw = $_POST['data'];
        $slug = $_POST['slug'];
        $ok = $this->blogs->update($slug, $user_id, $data_raw, $this->engine->render($data_raw));
        if (!$ok) {
            debug("updateing content: $slug failed!", __FILE__);
            http_response_code(HTTP_STATUS_SERVER_ERROR);
            exit(1);
        }

        session_write_close();
        http_response_code(HTTP_STATUS_SAVED);
        exit(0);
    }

    public function read_blog_raw(): void
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

        if (!isset($_GET['slug'])) {
            debug("read_raw: no path in request", __FILE__);
            http_response_code(HTTP_STATUS_BAD_REQUEST);
            exit(1);
        }

        $slug = $_GET['slug'];
        $content_raw = $this->blogs->get_raw($slug);
        if ($content_raw === null) {
            debug("read_raw: $slug not found in db", __FILE__);
            http_response_code(HTTP_STATUS_NOT_FOUND);
            exit(1);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo $content_raw;

        session_write_close();
        exit(0);
    }

    public function read_blog_feat(): void
    {
        ensure_request_method("GET");

        $feat = $this->blogs->get_feat();
        echo Helpers::renderNative(VIEWS . 'blog-feat.php', [
            'cover' => $feat['cover'],
            'title' => $feat['title'],
            'date' => $feat['updated_at'],
            'slug' => $feat['slug']
        ]);
        exit(0);
    }

    public function set_feat(): void
    {
        ensure_request_method("POST");

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

        if (!isset($_GET['slug'])) {
            debug("read_raw: no path in request", __FILE__);
            http_response_code(HTTP_STATUS_BAD_REQUEST);
            exit(1);
        }

        $slug = $_GET['slug'];
        if ($this->blogs->set_feat($slug)) {
            debug("new $slug set as featured.", __FILE__);
            echo "Done!";
            exit(0);
        } else {
            echo "Failed!";
            exit(1);
        }
    }

    public function read_blog_list(): void
    {
        ensure_request_method("GET");

        $blogs = $this->blogs->get_blogs();
        $output = '';
        foreach ($blogs as $b) {
            $output .= Helpers::renderNative(VIEWS . 'blog-list-single.php', [
                'slug' => $b['slug'],
                'cover' => $b['cover'],
                'title' => $b['title'],
                'date' => $b['updated_at'],
                'desc' => str_replace(',', ', ', $b['tags'])
            ]);
        }

        echo $output;
        exit(0);
    }

    public function read_blog_html(): void
    {
        ensure_request_method("GET");

        if (isset($_GET["slug"])) {
            $slug = $_GET["slug"];
            debug("slug from _GET with slug: $slug", __FILE__);
        } else {
            debug(var_export($_GET, true), __FILE__);
            echo "NOT FOUND";
            http_response_code(404);
            exit(1);
        }

        $content = $this->blogs->read_content_html($slug);
        if ($content === false) {
            debug("content not found", __FILE__);
            echo Helpers::renderNative(VIEWS . '404.html', []);
            http_response_code(404);
            exit(1);
        }

        echo Helpers::renderNative(VIEWS . 'skeleton-entry.php', [
            'slug' => $slug,
            'date' => $content['updated_at'],
            'title' => $content['title'],
            'blog' => $content['render'],
            'tags' => explode(',', $content['tags']),
            'cover' => $content['cover']
        ]);
        http_response_code(200);
        exit(0);
    }

    public function read_latest_blogs():void
    {
        ensure_request_method("GET");

        $blogs = $this->blogs->get_latest();
        $response = '<div class="flex flex-no-wrap overflow-x-auto no-scrollbar scrolling-touch items-start my-6" >';
        foreach ($blogs as $b) {
            $response .= Helpers::renderNative(VIEWS . 'home-blogs-list.php', $b);
        }
        $response .= '</div>';

        echo $response;
    }
}