<?php

namespace tinyfuse\controllers;

require_once MODELS . "UsersModel.php";

use BumpCore\EditorPhp\Helpers;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use tinyfuse\lib\Constants;
use tinyfuse\lib\Malachi;
use tinyfuse\models\UsersModel;

class Auth
{

    private string $method;
    private int $options;
    private string $iv;
    private mixed $key;

    public function __construct()
    {
        $this->key = $_ENV['MAGIC'] ?? '';
        $this->method = "AES-256-CBC";
        $this->iv = '2003051910111210';
        $this->options = 0;
    }

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
                session_start();
                session_regenerate_id();
                $_SESSION["email"] = $email;
                echo Helpers::renderNative(VIEWS . "login-ok.html", []);
                break;

            case Constants::NotFound:
                http_response_code(400);
                readfile(VIEWS . "login-400.html");
                exit(1);

            case Constants::InternalError:
            case Constants::NotAllowed:
            case Constants::BadRequest:
            case Constants::Created:
                http_response_code((int)Constants::InternalError);
                throw new Exception('Psuedostate Occured. This should not have happened!');
        }

        exit(0);
    }

    #[NoReturn] public function signin(): void
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            http_response_code(400);
            exit(1);
        }

        // TODO: sanitize before passing on
        $data = [
            "first_name" => $_POST["fname"],
            "last_name" => $_POST["lname"],
            "email" => $_POST["email"],
            "password" => $_POST["password"],
            "year_of_batch" => $_POST["year"],
            "country" => $_POST["country"],
            "address_line_1" => $_POST["address1"],
            "address_line_2" => $_POST["address2"],
            "zip_code" => $_POST["zipcode"],
            "telephone" => $_POST["telephone"],
            "role" => "0",
        ];

        $users = new UsersModel();
        $ok = $users->addNewUser($data);

        if ($ok === false) {
            http_response_code(400);
            readfile(VIEWS . "signin-400.html");
            exit(1);
        }

        // construct the code
        $code = "birnadinerick-sailakreeshan-nitharshan-mariothayan-fr-mahan-aloysius+" . $data['email'] . "+1850/" . date('m/d/H-i-s') . $this->key;
        $code = hash("sha256", $code);

        // construct the magiclink
        $magiclink = json_encode([
            "email" => $data['email'],
            "code" => $code,
        ]);
        $magiclink = openssl_encrypt($magiclink, $this->method, $this->key, $this->options, $this->iv);
        $magiclink = base64_encode($magiclink);

        switch ($users->addMagicCode($data['email'], $code)) {
            case true:
                break;

            case false:
                http_response_code(400);
                readfile(VIEWS . "signin-400.html");
                exit(1);
        }

        $m = new Malachi();
        $msg = Helpers::renderNative(VIEWS . 'email-user-activate.php', ['link' => SERVER . '/auth/activate?c=' . $magiclink]);

        switch ($m->send_msg('Activate your account', $msg, $data['email'])) {
            case true:
                readfile(VIEWS . "signin-ok.html");
                exit(0);

            case false:
                http_response_code(400);
                readfile(VIEWS . "signin-400.html");
                exit(1);
        }
    }

    public function activate_user(): void
    {
        $magiclink = base64_decode($_GET['code']);
        $magiclink = openssl_decrypt($magiclink, $this->method, $this->key, $this->options, $this->iv);
        $magiclink = json_decode($magiclink);

        $users = new UsersModel();
        if ($magiclink->code !== $users->getMagicCode($magiclink->email)) {
            echo "Wrong code. Contact Support!";
            exit(1);
        }

        switch ($users->activateUser($magiclink->email)) {
            case true:
                echo "All done";
                exit(0);
            case false:
                echo "Something wrong happened. Contact Support";
                exit(1);
        }
    }
}