<?php

namespace app\controllers;

use app\models\AuthModel;
use app\models\MagicCodeModel;
use tinyfuse\ACTION;
use tinyfuse\BaseController;
use tinyfuse\BaseState;
use tinyfuse\CryptoFunctions;
use tinyfuse\Mailer;
use tinyfuse\Request;
use tinyfuse\Response;
use tinyfuse\Utils;

class AuthController extends BaseController
{
    use CryptoFunctions, Mailer;

    private AuthModel $model;
    private MagicCodeModel $magic_model;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->model = new AuthModel($state);
        $this->magic_model = new MagicCodeModel($state);
    }

    public function login_user(Request $request): Response
    {
        session_regenerate_id(true);

        $params = $request->get_post_params();
        if (!isset($params['email']) || !isset($params['password'])) {
            return Response::forFailedAction();
        }

        $user_ok = $this->model->does_user_exists_and_active($params['email']);
        $creds_ok = $this->model->is_user_cred_valid($params['password']);
        if (!$user_ok && !$creds_ok) {
            return Response::forNotFound();
        }

        $_SESSION[SESSION_USER_LOGGED_IN] = '1';
        $_SESSION[SESSION_USER_EMAIL] = $params['email'];
        $_SESSION[SESSION_USER_ROLE] = strval($this->model->get_user_role($params['email']));
        return new Response('Logged in!');
    }

    public function register_user(Request $request): Response
    {
        $params = $request->get_post_params();
        $requiredFields = [
            "first_name", "last_name", "email", "password",
            "year_of_batch", "country", "address_line_1",
            "zip_code", "telephone"
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $_POST)) {
                $missingFields[] = $field;
            }
        }
        if (sizeof($missingFields) > 0) {
            Utils::logInfo(
                'AUTH_ERR: Following fields are missing from the request for new user:\n'
                .
                var_export($missingFields, true)
            );
            return Response::forFailedAction();
        }

        $magic_code = $this->gen_magic_code(ACTION::NEW_USER);
        $email_to = $params['email'];
        $email_to_name = $params['first_name'] . ' ' . $params['last_name'];
        $email_body = $this->render($this->state->VIEWS . 'email-user-activate', [
            'link' => $this->state->get_env('API') . 'activate-user&code=' . $magic_code
        ]);

        if (
            ($this->model->new_user($params) === false)
            || ($this->magic_model->add_magic_code($magic_code, $params['email']) === false)
            || ($this->mail_html($email_to, $email_to_name, $email_body) === false)
        ) {
            return Response::forFailedAction();
        }

        return new Response('All done. Please check your email inbox to activate your account');
    }

    public function activate_user(Request $request): Response
    {
        $magic_code = $request->get_get_param('code') ?? '';

        $magic_ok = $this->magic_model->validate_magic_code($magic_code, ACTION::NEW_USER);
        if ($magic_ok === false) {
            Utils::logInfo('AUTH_ERR: magic_transaction returned 404. code: ' . $magic_code);
            return Response::forNotFound();
        }

        if (
            !$this->model->activate_user($magic_ok)
            || !$this->magic_model->complete_magic_transaction($magic_code)
        ) {
            return Response::forFailedAction();
        }

        return new Response('Account Activated');
    }
}



























