<?php

namespace tinyfuse\controllers;

require_once MODELS . 'UsersModel.php';
require_once MODELS . 'BlogsModel.php';
require_once MODELS . 'comments.php';
require_once MODELS . 'contents.php';
require_once MODELS . 'ContentsV2Model.php';

use BumpCore\EditorPhp\Helpers;
use tinyfuse\models\BlogsModel;
use tinyfuse\models\UsersModel;


class Blogs
{
    private UsersModel $users;
    private BlogsModel $blogs;

    public function __construct()
    {
        $this->users = new UsersModel();
        $this->blogs = new BlogsModel();
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

}