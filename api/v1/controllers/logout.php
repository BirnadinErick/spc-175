<?php

function logout()
{
debug("attempt to log out...", __FILE__);
session_start();
debug("session is: " . var_export($_SESSION, true), __FILE__);

unset($_SESSION['email']);

debug("post-logout session is: " . var_export($_SESSION, true), __FILE__);
session_destroy();
debug("session destroyed!, re-routing...", __FILE__);

header("Location: http://localhost:2003/");
http_response_code(303);
exit();
}
