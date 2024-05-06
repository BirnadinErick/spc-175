<?php
// logging setup
error_reporting(E_ALL);
ini_set("display_errors", 0);

define("DEBUG", true);

function error_handler($errno, $errstr, $errfile, $errll)
{
    $time = time();
    $msg = "$time $errno [$errfile::$errll] | $errstr";
    error_log($msg . PHP_EOL, 3, "error_log.txt");

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
}

define("CONTROLLERS", "./v1/controllers/");
define("VIEWS", $_SERVER['DOCUMENT_ROOT'] . "/v1/views/");
define("MODELS", $_SERVER['DOCUMENT_ROOT'] . "/v1/models/");

include_once(CONTROLLERS . "signin.php");
include_once(CONTROLLERS . "login.php");
include_once(CONTROLLERS . "logout.php");
include_once(CONTROLLERS . "auth-state.php");

$routes = [
    '/api/v1/signin' => 'signin',
    '/api/v1/login' => 'login',
    '/api/v1/logout' => 'logout',
    '/api/v1/auth-state' => 'auth_state',
];
$request_uri = $_SERVER['REQUEST_URI'];

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

