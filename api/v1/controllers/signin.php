<?php
define("VIEWS", $_SERVER['DOCUMENT_ROOT']."/v1/views/");

function signin() {
  // Check if form is submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Fetch form inputs
    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $year = $_POST["year"];
    $country = $_POST["country"];
    $address1 = $_POST["address1"];
    $address2 = $_POST["address2"];
    $zipcode = $_POST["zipcode"];
    $telephone = $_POST["telephone"];

    // Open stdout
    $stdout = fopen('php://stdout', 'w');

    // Output form inputs to stdout
    fwrite($stdout, "First Name: $fname\n");
    fwrite($stdout, "Last Name: $lname\n");
    fwrite($stdout, "Email: $email\n");
    fwrite($stdout, "Password: $password\n");
    fwrite($stdout, "Year of the Batch: $year\n");
    fwrite($stdout, "Country of Residence: $country\n");
    fwrite($stdout, "Address Line 1: $address1\n");
    fwrite($stdout, "Address Line 2: $address2\n");
    fwrite($stdout, "Zip Code: $zipcode\n");
    fwrite($stdout, "Telephone: $telephone\n");

    // Close stdout
    fclose($stdout);

    readfile(VIEWS."signin-ok.php");
  } else {
    // If not a POST request, show an error message
    echo "Error: This script accepts only POST requests.";
  }
}

?>

