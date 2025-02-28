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
        $email_body = $this->render($this->state->VIEWS . 'email-user-activate', ['link' => $magic_code]);
        if (
            ($this->model->new_user($params) === false)
            || ($this->magic_model->add_magic_code($magic_code, $params['email']) === false)
            || ($this->mail_html($email_to, $email_to_name, $email_body) === false)
        ) {
            return Response::forFailedAction();
        }

        return new Response('All done. Please check your email inbox to activate your account');
    }
}