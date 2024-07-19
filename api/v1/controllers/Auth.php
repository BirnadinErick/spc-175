<?php

namespace tinyfuse\controllers;

require_once MODELS . "UsersModel.php";

use BumpCore\EditorPhp\Helpers;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use tinyfuse\lib\Constants;
use tinyfuse\models\UsersModel;

class Auth
{
    #[NoReturn] public function auth_state(): void
    {
        session_start();
        debug("auth_state handler invoke", __FILE__);

        if (isset($_SESSION["email"]) === false) {
            echo Helpers::renderNative(VIEWS . 'noauth.php', ["login" => SERVER . '/auth/login', "sigin" => SERVER . '/auth/signin']);
            exit(0);
        }

        $user = new UsersModel();
        header("Cache-Control: max-age=180");

        $user_name = $user->get_decorated_name($_SESSION["email"]);
        $is_user_editor = $user->check_roles_exist(EDITOR_ROLE, $_SESSION["email"]);
        $is_user_sadmin = $user->check_roles_exist(SUPADMIN_ROLE, $_SESSION["email"]);

        echo Helpers::renderNative(VIEWS . "auth-ok-navbar.php", ["is_user_editor" => $is_user_editor, "is_user_sadmin" => $is_user_sadmin, "user_name" => $user_name]);
        session_write_close();
        exit(0);
    }

    /**
     * @throws Exception
     */
    public function login(): void
    {
        if (isset($_SESSION["email"])) {
            header("Location: " . SERVER . "/");
            http_response_code(303);
            exit(0);
        }


        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            http_response_code(400);
            exit(1);
        }

        $email = $_POST["email"];
        $password = $_POST["password"];

        $users = new UsersModel();

        switch ($users->authenticateUser($email, $password)) {
            case Constants::OK:
                http_response_code(400);
                readfile(VIEWS . "login-400.html");
                break;

            case Constants::NotFound:
            case Constants::InternalError:
                http_response_code((int)Constants::InternalError);
                exit(1);
            case Constants::NotAllowed:
            case Constants::BadRequest:
            case Constants::Created:
                throw new Exception('Psuedostate Occured. This should not have happened!');
        }

        session_start();
        session_regenerate_id();
        $_SESSION["email"] = $email;

        echo Helpers::renderNative(VIEWS . "login-ok.html", []);
        exit(0);
    }
}