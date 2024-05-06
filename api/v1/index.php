<?php
// logging setup
error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", "On");

define("DEBUG", false);

function error_handler($errno, $errstr, $errfile, $errll)
{
    $time = time();
    $msg = "$time $errno [$errfile::$errll] | $errstr";
    error_log($msg . PHP_EOL, 3, "error_log.txt");
    file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/error_log.bk.txt', $msg, FILE_APPEND);

    if (DEBUG) {
        $msg = "[$errfile] $errstr";
        error_log($msg . PHP_EOL, 3, "debug_log.txt");
    }
}

set_error_handler("error_handler");

function debug($str, $file)
{
    $msg = "[$file] $str";
    error_log($msg . PHP_EOL, 3, "debug_log.txt");
    file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/debug_log.bk.txt', $msg, FILE_APPEND);
}

debug(var_export($_SERVER, true), __FILE__);

define("CONTROLLERS", $_SERVER['DOCUMENT_ROOT'] . "/api/v1/controllers/");
define("VIEWS", $_SERVER['DOCUMENT_ROOT'] . "/api/v1/views/");
define("MODELS", $_SERVER['DOCUMENT_ROOT'] . "/api/v1/models/");

if (DEBUG) {
    define("SERVER", "http://localhost:2007");
    define("API", "http://localhost:2003/api/v1/index.php?p=");
} else {
    define("SERVER", "https://www.spcjaffna-beta.org");
    define("API", "https://www.spcjaffna-beta.org/api/v1/index.php?p=");
}

include_once(CONTROLLERS . "signin.php");
include_once(CONTROLLERS . "login.php");
include_once(CONTROLLERS . "logout.php");
include_once(CONTROLLERS . "auth-state.php");

$routes = [
    'signin' => 'signin',
    'login' => 'login',
    'logout' => 'logout',
    'auth-state' => 'auth_state',
];
$request_uri = $_GET["p"];

if (array_key_exists($request_uri, $routes)) {
    $handler = $routes[$request_uri];

    debug("routed to $handler by $request_uri", __FILE__);

    if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
        header("Access-Control-Allow-Credentials: true");
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: *');
        header("Access-Control-Allow-Origin: *");

        http_response_code(200);

        debug("preflight detected", __FILE__);
        die(0);
    }

    $handler();
    http_response_code(200);
    exit();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
    exit();
}

