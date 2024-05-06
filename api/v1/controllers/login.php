<?php

require_once(MODELS . "users.php");

function login()
{

if (isset($_SESSION['email'])){
    debug("loggin user in login route!", __FILE__);
    debug("session is: ". var_export($_SESSION, true), __FILE__);
    header("Location: " . SERVER . "/");
    http_response_code(303);
    die(0);
}

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        debug("recieved post data: ". var_export($_POST, true), __FILE__);

        $email = $_POST["email"];
        $password = $_POST["password"];
        $users = new UsersModel();
        $data = [
            'email' => $email,
            'password' => $password,
        ];

        $ok = $users->authenticateUser($email, $password);

        if ($ok === false) {
            debug("user authetication failed", __FILE__);
            http_response_code(400);
            readfile(VIEWS . "login-400.html");
            exit();
        }

        debug("user authenticated", __FILE__);
        session_start();
        session_regenerate_id();
        $_SESSION['email'] = $email;
        debug("current session state: " . var_export($_SESSION, true), __FILE__);
        readfile(VIEWS . "login-ok.html");
    } else {
        debug("wrong http method on route", __FILE__);
        echo "Error: This script accepts only POST requests.";
    }
}
