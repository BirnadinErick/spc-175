<?php

error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", "On");

define("DEBUG", true);

function error_handler(int $errno, string $errstr, string $errfile, int $errll)
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

function debug(string $str, string $file)
{
    $msg = "[$file] $str";
    error_log($msg . PHP_EOL, 3, "debug_log.txt");
}

/* IAM Roles Def
 * - use `|=` op to combine roles
 * - use `&` op to check for role availability
 * - use `&= ~` to remove a role
 * - any user has VISITOR role as default
 */
define('VISITOR_ROLE', 0);  // default role (can read pages/posts/projects or own users record)
define('EDITOR_ROLE', 1 << 0);  // write permission to posts and page
define('PROJMOD_ROLE', 1 << 1);  // can change status, est. value and deadline etc. in project Long
define('PROJADMIN_ROLE', 1 << 2);  // write permission on projects
define('SUPADMIN_ROLE', 1 << 3); // write permission on users !!CAREFUL

// REPO Common PATH Def
define("CONTROLLERS", $_SERVER["DOCUMENT_ROOT"] . "/api/v1/controllers/");
define("VIEWS", $_SERVER["DOCUMENT_ROOT"] . "/api/v1/views/");
define("MODELS", $_SERVER["DOCUMENT_ROOT"] . "/api/v1/models/");
define("APP", $_SERVER["DOCUMENT_ROOT"] . "/api/v1/");

if (DEBUG) {
    define("SERVER", "http://localhost:2007");
    define("API", "http://localhost:2004/api/v1/index.php?p=");
    define("ENV", ".dev");
} else {
    define("SERVER", "https://www.spcjaffna-beta.org");
    define("API", "https://www.spcjaffna-beta.org/api/v1/index.php?p=");
    define("ENV", ".prod");
}

require __DIR__ . '/vendor/autoload.php';

// env var initialization
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ENV);
$dotenv->load();

include_once CONTROLLERS . "signin.php";
include_once CONTROLLERS . "login.php";
include_once CONTROLLERS . "logout.php";
include_once CONTROLLERS . "auth-state.php";
include_once CONTROLLERS . "comments.php";
include_once CONTROLLERS . "allowed-to-comment.php";
include_once CONTROLLERS . "projects.php";
include_once CONTROLLERS . "posts.php";

require_once CONTROLLERS . "Auth.php";
require_once APP."lib/Malachi.php";

use tinyfuse\controllers\Auth;

$auth = new Auth();

$routes = [
    "signin" => [$auth, "signin"],
    "login" => [$auth, "login"],
    "logout" => "logout",
    "activate-user"=> [$auth, "activate_user"],
    "auth-state" => [$auth, "auth_state"],
    "comments" => "comments",
    "allowed-to-comment" => "allowed_to_comment",
    "projects" => "projects",
    "save-post" => "save_post",
    "read-post" => "read_post",
    "read-post-html" => "read_post_html",
    "create-post" => "create_post",
    "available-contents" => "available_contents",
    "save-blog" => "save_blog",
    "create-blog" => "create_blog",
    "read-blog-html" => "read_blog_html",
    "read-blog-feat" => "read_blog_feat",
    "read-blog-list" => "read_blog_list",
    "read-blogs-latest" => "read_blogs_latest",
];
$request_uri = $_GET["p"];

if (array_key_exists($request_uri, $routes)) {
    $handler = $routes[$request_uri];

//    debug("ROUTER: $handler[1]", __FILE__);

    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Origin: *");

        http_response_code(200);
        die(0);
    }

    // only during migration phase
    if (gettype($handler) === "string") {
        $handler();
    } else {
        [$controller, $method] = $handler;
        $controller->$method();
    }
} else {
    http_response_code(404);
    echo json_encode(["error" => "Route not found"]);
}
exit();
