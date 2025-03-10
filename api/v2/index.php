<?php declare(strict_types=1);

use app\controllers\AuthController;
use app\controllers\HomeController;
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

// hook routing controllers
$state->addRoute(ROUTE_METHOD_GET, 'hello', [$home, 'home']);
$state->addRoute(ROUTE_METHOD_POST, 'count', [$home, 'count']);
$state->addRoute(ROUTE_METHOD_GET, 'servus', [$home, 'servus']);

$state->addRoute(ROUTE_METHOD_POST, 'register-user', [$auth, 'register_user']);
$state->addRoute(ROUTE_METHOD_GET, 'activate-user', [$auth, 'activate_user']);
$state->addRoute(ROUTE_METHOD_POST, 'login-user', [$auth, 'login_user']);
$state->addRoute(ROUTE_METHOD_POST, 'logout-user', [$auth, 'logout_user']);
$state->addRoute(ROUTE_METHOD_POST, 'initiate-password-reset', [$auth, 'initiate_password_reset']);
$state->addRoute(ROUTE_METHOD_POST, 'complete-password-reset', [$auth, 'complete_password_reset']);

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