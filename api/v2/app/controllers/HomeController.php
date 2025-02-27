<?php

namespace app\controllers;

use app\models\SettingsModel;
use tinyfuse\BaseState;
use tinyfuse\Request;
use tinyfuse\Response;

class HomeController
{
    private SettingsModel $settings;

    public function __construct(BaseState $state)
    {
        $this->settings = new SettingsModel($state);
    }

    public function home(Request $request): Response
    {
        $content = 'Hello from controller';
        $content .= ' count: ' . $this->settings->get_count();
        return new Response($content);
    }

}