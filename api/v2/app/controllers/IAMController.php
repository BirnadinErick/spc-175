<?php

namespace app\controllers;

use app\models\AuthModel;
use tinyfuse\BaseController;
use tinyfuse\BaseState;
use tinyfuse\Request;
use tinyfuse\Response;
use tinyfuse\UserRole;

class IAMController extends BaseController
{
    private readonly AuthModel $auth_model;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->auth_model = new AuthModel($state);
    }

    public function change_user_role(Request $request): Response
    {
        $params = $request->get_post_params();

        // check if necessary variables are already set and not null
        if (!isset($_SESSION[SESSION_USER_LOGGED_IN]) && !isset($_SESSION[SESSION_USER_ROLE])
            && !isset($params['email']) && !isset($params['new_role'])
        ) {
            return Response::forNotAllowed();
        }

        // check whether actor has enough permission
        if ($_SESSION[SESSION_USER_LOGGED_IN] === '1' && $_SESSION[SESSION_USER_ROLE] !== UserRole::SUPER_ADMIN->value) {
            return Response::forNotAllowed();
        }

        if (!$this->auth_model->change_user_role($params['email'], $params['new_role'])) {
            return Response::forFailedAction();
        }

        return new Response('Action succeeded');
    }
}