<?php

function logout()
{
    debug("attempt to log out...", __FILE__);
    session_start();

    unset($_SESSION["email"]);

    debug("post-logout session is: " . var_export($_SESSION, true), __FILE__);
    session_destroy();
    debug("session destroyed!, re-routing...", __FILE__);

    header("Location: " . SERVER . "/");
    http_response_code(303);
    exit();
}
