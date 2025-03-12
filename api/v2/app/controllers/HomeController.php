<?php

namespace app\controllers;

use app\models\SettingsModel;
use tinyfuse\BaseController;
use tinyfuse\BaseState;
use tinyfuse\Request;
use tinyfuse\Response;
use tinyfuse\Utils;

class HomeController extends BaseController
{
    private SettingsModel $settings;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->settings = new SettingsModel($state);
        $this->state = $state;
    }

    public function servus(Request $request): Response
    {
        $content = $this->render($this->state->VIEWS . 'servus', ['name' => $_SESSION[SESSION_USER_EMAIL] ?? 'Anonymity']);
        return new Response($content);
    }

    public function count(Request $request): Response
    {
        $params = $request->get_post_params();
        $new_count = $this->settings->set_count((int)$params['value']) ?? 0;
        return new Response('new count: ' . $new_count);
    }

    public function home(Request $request): Response
    {
        $content = 'Hello from controller';
        $content .= ' count: ' . $this->settings->get_count();
        return new Response($content);
    }

}