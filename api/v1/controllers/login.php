<?php
require_once MODELS . "users.php";
require_once APP. "lib/Constants.php";

use tinyfuse\lib\Constants;

function login()
{
    if (isset($_SESSION["email"])) {
        header("Location: " . SERVER . "/");
        http_response_code(303);
        exit(0);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST["email"];
        $password = $_POST["password"];

        $users = new UsersModel();

        switch ($users->authenticateUser($email, $password) ){
            case Constants::OK:
                http_response_code(400);
                readfile(VIEWS . "login-400.html");
                break;

            case Constants::NotFound:
            case Constants::InternalError:
                http_response_code((int)Constants::InternalError);
        }

        session_start();
        session_regenerate_id();
        $_SESSION["email"] = $email;
        readfile(VIEWS . "login-ok.html");
    } else {
        http_response_code(400);
        exit(1);
    }
}
