<?php

require_once MODELS . "users.php";

function signin()
{
    debug("sigin flow init", __FILE__);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        debug("attempt new user...", __FILE__);

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
            debug("failed to create user", __FILE__);
            http_response_code(400);
            readfile(VIEWS . "signin-400.php");
            die();
        }

        session_start();
        $_SESSION["email"] = $data["email"];
        http_response_code(201);
        readfile(VIEWS . "signin-ok.html");
        session_write_close();
        die(0);
    } else {
        http_response_code(400);
        echo "Error: This script accepts only POST requests.";
        die(1);
    }
}
