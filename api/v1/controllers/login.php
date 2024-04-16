<?php
session_start();

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
      readfile(VIEWS."login-400.html");
      die();
    }

    $_SESSION['email'] = $email;
    readfile(VIEWS."login-ok.html");
  } else {
    echo "Error: This script accepts only POST requests.";
  }
}

?>
