<?php
session_start();

require_once(MODELS."users.php");

function signin() {

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // TODO: sanitize before passing on
    $data = [
      'first_name' => $_POST["fname"],
      'last_name' => $_POST["lname"],
      'email' => $_POST["email"],
      'password' => $_POST["password"],
      'year_of_batch' => $_POST["year"],
      'country' => $_POST["country"],
      'address_line_1' => $_POST["address1"],
      'address_line_2' => $_POST["address2"],
      'zip_code' => $_POST["zipcode"],
      'telephone' => $_POST["telephone"],
    ];

    // Open stdout
    // TODO: Log instead of stdout
    $stdout = fopen('php://stdout', 'w');

    // Output form inputs to stdout
    fwrite($stdout, "First Name: {$data['first_name']}\n");
    fwrite($stdout, "Last Name: {$data['last_name']}\n");
    fwrite($stdout, "Email: {$data['email']}\n");
    fwrite($stdout, "Password: {$data['password']}\n");
    fwrite($stdout, "Year of the Batch: {$data['year_of_batch']}\n");
    fwrite($stdout, "Country of Residence: {$data['country']}\n");
    fwrite($stdout, "Address Line 1: {$data['address_line_1']}\n");
    fwrite($stdout, "Address Line 2: {$data['address_line_2']}\n");
    fwrite($stdout, "Zip Code: {$data['zip_code']}\n");
    fwrite($stdout, "Telephone: {$data['telephone']}\n");

    // Close stdout
    fclose($stdout);

    $users = new UsersModel();
    $ok = $users->addNewUser($data);

    if ($ok === false) {
      http_response_code(400);
      readfile(VIEWS."signin-400.php");
      die();
    }

    $_SESSION['email'] = $data['email'];
    readfile(VIEWS."signin-ok.html");
  } else if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    echo "Yup, Fide et Labore";
  } else {

    http_response_code(400);
    echo "Error: This script accepts only POST requests.";
  }
}

?>

