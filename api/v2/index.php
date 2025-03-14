<?php declare(strict_types=1);

use app\controllers\AuthController;
use app\controllers\BlogController;
use app\controllers\ContentController;
use app\controllers\HomeController;
use app\controllers\IAMController;
use app\controllers\ProjectController;
use Dotenv\Dotenv;
use tinyfuse\BaseState as State;
use tinyfuse\Kernel;
use tinyfuse\Request;
use tinyfuse\Utils;

const ROOT = __DIR__;
require_once ROOT . '/vendor/autoload.php';
require_once ROOT.'/tinyfuse/Constants.php';

// in prod, change the env file to .prod
$dotenv = Dotenv::createImmutable(ROOT, '.dev');
$dotenv->load();

Utils::logRequest();

$state = State::default();

// init controllers
$home = new HomeController($state);
$auth = new AuthController($state);
$iam = new IAMController($state);
$content = new ContentController($state);
$blog = new BlogController($state);
$projects = new ProjectController($state);

/* routing hooks configuration */

// home
$state->addRoute(ROUTE_METHOD_GET, 'hello', [$home, 'home']);
$state->addRoute(ROUTE_METHOD_POST, 'count', [$home, 'count']);
$state->addRoute(ROUTE_METHOD_GET, 'servus', [$home, 'servus']);

// auth
$state->addRoute(ROUTE_METHOD_GET, 'auth-state', [$auth, 'auth_state']);
$state->addRoute(ROUTE_METHOD_POST, 'register-user', [$auth, 'register_user']);
$state->addRoute(ROUTE_METHOD_GET, 'activate-user', [$auth, 'activate_user']);
$state->addRoute(ROUTE_METHOD_POST, 'login-user', [$auth, 'login_user']);
$state->addRoute(ROUTE_METHOD_POST, 'logout-user', [$auth, 'logout_user']);
$state->addRoute(ROUTE_METHOD_POST, 'initiate-password-reset', [$auth, 'initiate_password_reset']);
$state->addRoute(ROUTE_METHOD_POST, 'complete-password-reset', [$auth, 'complete_password_reset']);

// content
$state->addRoute(ROUTE_METHOD_GET, 'get-content-html', [$content, 'get_content_html']);
$state->addRoute(ROUTE_METHOD_GET, 'get-content-raw', [$content, 'get_content_raw']);
$state->addRoute(ROUTE_METHOD_GET, 'editable-contents', [$content, 'editable_contents']);
$state->addRoute(ROUTE_METHOD_POST, 'update-content', [$content, 'update_content']);

// blogs
$state->addRoute(ROUTE_METHOD_GET, 'get-blog-html', [$blog, 'get_blog_html']);
$state->addRoute(ROUTE_METHOD_GET, 'get-blog-raw', [$blog, 'get_blog_raw']);
$state->addRoute(ROUTE_METHOD_GET, 'editable-blogs', [$blog, 'editable_blogs']);
$state->addRoute(ROUTE_METHOD_POST, 'update-blog', [$blog, 'update_blog']);

// IAM
$state->addRoute(ROUTE_METHOD_POST, 'change-user-role', [$iam, 'change_user_role']); // TODO: not tested

// project
$state->addRoute(ROUTE_METHOD_GET, 'get-project-list', [$projects, 'get_project_list']);
$state->addRoute(ROUTE_METHOD_GET, 'get-project-detail', [$projects, 'get_project_detail']);
$state->addRoute(ROUTE_METHOD_POST, 'request-new-project', [$projects, 'request_new_project']);
$state->addRoute(ROUTE_METHOD_GET, 'admin-project-list', [$projects, 'admin_project_list']);
$state->addRoute(ROUTE_METHOD_GET, 'project-edit-form', [$projects, 'project_edit_form']);
$state->addRoute(ROUTE_METHOD_POST, 'update-project', [$projects, 'update_project']);

/* end routing hooks configuration */

/* session setup */
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.name', 'SPCMotherSessID');
/* end session setup */

session_start();
$req = Request::fromGlobals();
$res = Kernel::handle($req, $state);
session_write_close(); // regardless of op-result, we say 200;
$res->send();

exit(0);