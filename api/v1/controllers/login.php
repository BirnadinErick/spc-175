<?php

require_once(MODELS."users.php");

function login() {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];
    $stdout = fopen('php://stdout', 'w');

    fwrite($stdout, "Email: $email\n");
    fwrite($stdout, "Password: $password\n");

    fclose($stdout);

    $users = new UsersModel();
      $data = [
          'email'=> $email,
        'password'=> $password,
      ];
      $ok = $users->authenticateUser($email, $password);

    if ($ok === false) {
      http_response_code(400);
      readfile(VIEWS."signin-400.php");
      die();
    }

    readfile(VIEWS."login-ok.php");
  } else {
    echo "Error: This script accepts only POST requests.";
  }
}

?>
