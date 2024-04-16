<?php
define("CONTROLLERS", "./v1/controllers/");
define("VIEWS", $_SERVER['DOCUMENT_ROOT'] . "/v1/views/");
define("MODELS", $_SERVER['DOCUMENT_ROOT'] . "/v1/models/");

require_once(CONTROLLERS . "signin.php");
require_once(CONTROLLERS . "login.php");
require_once(CONTROLLERS . "logout.php");
require_once(CONTROLLERS . "auth-state.php");

$routes = [
    '/api/v1/signin' => 'signin',
    '/api/v1/login' => 'login',
    '/api/v1/logout' => 'logout',
    '/api/v1/auth-state' => 'auth_state',
];
$request_uri = $_SERVER['REQUEST_URI'];

if (array_key_exists($request_uri, $routes)) {
    $handler = $routes[$request_uri];

    header("Access-Control-Allow-Credentials: true");
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    header("Access-Control-Allow-Origin: *");

    $handler();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}

?>
