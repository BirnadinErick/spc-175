<?php

namespace app\controllers;

use tinyfuse\BaseState;
use tinyfuse\Request;
use tinyfuse\Response;

class HomeController
{
    public function home(Request $request, BaseState $state): Response
    {
        $content = 'Hello from controller';
        return new Response($content);
    }

}