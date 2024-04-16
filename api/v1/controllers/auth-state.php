<?php

function auth_state()
{
    if (isset($_SESSION['email'])) {
        readfile(VIEWS . "AuthBannerPartial.html");
    } else {
        readfile(VIEWS . "UnAuthBannerPartial.html");
    }

    die();
}
?>