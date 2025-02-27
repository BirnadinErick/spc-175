<?php declare(strict_types=1);

use App\State;
use Dotenv\Dotenv;
use Tinyfuse\Http\Request;
use Tinyfuse\Kernel\Kernel;

const ROOT = __DIR__;
require_once ROOT. '/vendor/autoload.php';

// in prod, change the env file to .prod
$dotenv = Dotenv::createImmutable(ROOT, '.dev');
$dotenv->load();

// int new state and add routes
$state = new State($_ENV);
$state->addRoute('GET', 'hello', ['hello','jk']);

session_start();
$req = Request::fromGlobals();
$res = Kernel::handle($req, $state);
session_write_close(); // regardless of op-result, we say 200;
$res->send();
exit(0);
