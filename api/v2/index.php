<?php declare(strict_types=1);

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

// hook routing controllers
$state->addRoute(ROUTE_METHOD_GET, 'hello', [$home, 'home']);
$state->addRoute(ROUTE_METHOD_POST, 'count', [$home, 'count']);

session_start();
$req = Request::fromGlobals();
$res = Kernel::handle($req, $state);
session_write_close(); // regardless of op-result, we say 200;
$res->send();

exit(0);