<?php

error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", "On");

const DEBUG = true;

// flags
const FLAGS_AUTH = true;
const LOG_DEBUG_FS = true;
const MALACHI = false;

// status codes
const HTTP_STATUS_BAD_REQUEST = 400;
const HTTP_STATUS_UNAUTHORIZED = 401;
const HTTP_STATUS_NOT_FOUND = 404;
const HTTP_STATUS_SAVED = 204;
const HTTP_STATUS_CREATED = 201;
const HTTP_STATUS_SERVER_ERROR = 500;

function error_handler(int $errno, string $errstr, string $errfile, int $errll): void
{
    $time = time();
    $msg = "$time $errno [$errfile::$errll] | $errstr";
    error_log($msg . PHP_EOL, 3, "error_log.txt");

    if (LOG_DEBUG_FS) {
        $msg = "ERR[$errfile] $errstr";
        error_log($msg . PHP_EOL, 3, "debug_log.txt");
    }
}

set_error_handler("error_handler");

function debug(string $str, string $file): void
{
    if (!LOG_DEBUG_FS) {
        return;
    }

    $msg = "[$file] $str";
    error_log($msg . PHP_EOL, 3, "debug_log.txt");
}

function current_time(): string
{
    return date('Y-m-d');
}

/* IAM Roles Def
 * - use `|=` op to combine roles
 * - use `&` op to check for role availability
 * - use `&= ~` to remove a role
 * - any user has VISITOR role as default
 */
const VISITOR_ROLE = 0;  // default role (can read pages/posts/projects or own users record)
const EDITOR_ROLE = 1 << 0;  // write permission to posts and page
const PROJMOD_ROLE = 1 << 1;  // can change status, est. value and deadline etc. in project Long
const PROJADMIN_ROLE = 1 << 2;  // write permission on projects
const SUPADMIN_ROLE = 1 << 3; // write permission on users !!CAREFUL

// REPO Common PATH Def
define("CONTROLLERS", $_SERVER["DOCUMENT_ROOT"] . "/api/v1/controllers/");
define("VIEWS", $_SERVER["DOCUMENT_ROOT"] . "/api/v1/views/");
define("MODELS", $_SERVER["DOCUMENT_ROOT"] . "/api/v1/models/");
define("APP", $_SERVER["DOCUMENT_ROOT"] . "/api/v1/");
define("LIB", $_SERVER["DOCUMENT_ROOT"] . "/api/v1/lib/");

if (DEBUG) {
    define("SERVER", "http://localhost:2007");
    define("API", "http://localhost:2004/api/v1/index.php?p=");
    define("ENV", ".dev");
} else {
    define("SERVER", "http://dev.spcjaffna.org");
    define("API", "http://deev.spcjaffna.org/api/v1/index.php?p=");
    define("ENV", ".prod");
}

// utils

function ensure_request_method(string $method_to_check): void
{
    if ($_SERVER["REQUEST_METHOD"] !== $method_to_check) {
        http_response_code(400);
        echo("Our Engineers screwed up something, sorry. Please refresh the page");
        exit(1);
    }
}

require __DIR__ . '/vendor/autoload.php';

// env var initialization
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ENV);
$dotenv->load();

require_once LIB . 'BaseModel.php';
require_once MODELS . 'UsersModel.php';
require_once MODELS . 'ContentsV2Model.php';
require_once MODELS . 'BlogsModel.php';
require_once MODELS . 'ProjectsModelV2.php';

// checks if the current session is authenticated or not
// if control flow passes this function, then it is assumed
// the session had `email` set
// returns the email address if user is logged in
function check_authd(): string
{
    session_start();
    if (!isset($_SESSION["email"])) {
        debug("annonymous post save attempt", __FILE__);
        http_response_code(401);
        exit(1);
    }

    $email = $_SESSION['email'];
    session_write_close();
    return $email;
}

include_once CONTROLLERS . "signin.php";
include_once CONTROLLERS . "login.php";
include_once CONTROLLERS . "logout.php";
include_once CONTROLLERS . "auth-state.php";
include_once CONTROLLERS . "comments.php";
include_once CONTROLLERS . "allowed-to-comment.php";
include_once CONTROLLERS . "posts.php";
include_once CONTROLLERS . "feedback.php";

require_once CONTROLLERS . "Auth.php";
require_once CONTROLLERS . "Projects.php";
require_once CONTROLLERS . "Contents.php";
require_once CONTROLLERS . "Blogs.php";

require_once APP . "lib/Malachi.php";
require_once APP . "lib/contentengine/ContentEngine.php";

// settings & keys
require_once APP . 'lib/Settings.php';
const SETTINGS_BLOG_FEAT_KEY = "blogs_current_featured";

use tinyfuse\controllers\Auth;
use tinyfuse\controllers\Blogs;
use tinyfuse\controllers\Contents;
use tinyfuse\controllers\Projects;

$auth = new Auth();
$projects = new Projects();
$contents = new Contents();
$blogs = new Blogs();

$routes = [
    "signin" => [$auth, "signin"],
    "login" => [$auth, "login"],
    "logout" => "logout",
    "activate-user" => [$auth, "activate_user"],
    "auth-state" => [$auth, "auth_state"],
    "mobile-auth-state" => [$auth, "mobile_auth_state"],

    "comments" => "comments",
    "allowed-to-comment" => "allowed_to_comment",

    "projects" => [$projects, "list"],
    "get-project-comment" => [$projects, "get_comments"],
    "available-projects" => [$projects, "available_projects"],
    "projects-edit" => [$projects, "projects_edit"],
    "project-detail" => [$projects, "detail"],
    "new-project-comment" => [$projects, "new_comment"],

    "save-post" => "save_post",
    "read-post" => "read_post",
    "read-post-html" => [$contents, "read_content_html"],
    "read-post-raw" => "read_post_raw",
    "create-post" => "create_post",
    "available-contents" => "available_contents",

    /* contents v2 */
    "read-content-raw" => [$contents, "read_content_raw"],
    "update-content" => [$contents, "update_content"],
    "delete-content" => [$contents, "delete_content"],
    // "migrate-contentv2" => [$contents, "migrate"],
    "editable-contents" => [$contents, "editable_contents"],

    "read-blogs-latest" => [$blogs, "read_latest_blogs"],
    "read-blog-html" => [$blogs, "read_blog_html"],
    "create-blog" => [$blogs, "create_blog"],
    "editable-blogs" => [$blogs, "editable_blogs"],
    "delete-blog" => [$blogs, "delete_blog"],
    "read-blog-raw" => [$blogs, "read_blog_raw"],
    "update-blog" => [$blogs, "update_blog"],
    "read-blog-list" => [$blogs, "read_blog_list"],
    "read-blog-feat" => [$blogs, "read_blog_feat"],
    "set-blog-feat" => [$blogs, "set_feat"],

    "feedback" => "feedback"

    // "migrate" => "migrate"
];
$request_uri = $_GET["p"];

if (array_key_exists($request_uri, $routes)) {
    $handler = $routes[$request_uri];

    $allowedOrigins = ['https://spcjaffna-beta.org', 'https://www.spcjaffna-beta.org'];
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, $allowedOrigins)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
        header("Access-Control-Max-Age: 3600"); // 1 hr
    }
    if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
        http_response_code(200);
        exit(0);
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
