<?php /** @noinspection PhpUnusedParameterInspection */

namespace app\controllers;

use app\models\AuthModel;
use app\models\MagicCodeModel;
use tinyfuse\ACTION;
use tinyfuse\AuthUtils;
use tinyfuse\BaseController;
use tinyfuse\BaseState;
use tinyfuse\CryptoFunctions;
use tinyfuse\IAMUtils;
use tinyfuse\Mailer;
use tinyfuse\Request;
use tinyfuse\Response;
use tinyfuse\UserRole;
use tinyfuse\Utils;

class AuthController extends BaseController
{
    use CryptoFunctions, Mailer, AuthUtils, IAMUtils;

    private AuthModel $model;
    private MagicCodeModel $magic_model;
    public string $auth_views_root;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->model = new AuthModel($state);
        $this->magic_model = new MagicCodeModel($state);
        $this->auth_views_root = $state->VIEWS . 'auth/';
    }

    private function get_auth_state_params(): array
    {
        return array_merge($this->get_user_roles_matrix(
            UserRole::from($this->model->get_user_role($this->get_user_email() ?? ''))),
            [
                "username" => $this->model->get_user_display_name($this->get_user_email() ?? ''),
                "logout_path" => ($this->state->get_env('API') ?? '') . 'logout-user'
            ]
        );

    }

    public function auth_state(Request $_): Response
    {
        if ($this->is_anon_user()) {
            $content = $this->render($this->auth_views_root . 'state-not-authed', []);
        } else {
            $content = $this->render($this->auth_views_root . 'state-authed', $this->get_auth_state_params());
        }

        return new Response($content);
    }

    public function login_user(Request $request): Response
    {
        session_regenerate_id(true);

        $params = $request->get_post_params();
        if (!isset($params['email']) || !isset($params['password'])) {
            return Response::forFailedAction();
        }

        $user_ok = $this->model->does_user_exists_and_active($params['email']);
        $creds_ok = $this->model->is_user_cred_valid($params['password'], $params['email']);
        if (!$user_ok || !$creds_ok) {
            return new Response($this->render(
                $this->auth_views_root . 'login-failed.html', [], false
            ));
        }
        $user_role = strval($this->model->get_user_role($params['email']));

        $_SESSION[SESSION_USER_LOGGED_IN] = '1';
        $_SESSION[SESSION_USER_EMAIL] = $params['email'];
        $_SESSION[SESSION_USER_ROLE] = $user_role;

        $desktop_navbar = $this->render($this->auth_views_root . 'state-authed', $this->get_auth_state_params());
        $mobile_navbar = "";
        return new Response($this->render(
            $this->auth_views_root . 'login-ok',
            params: [
                "desktop_navbar_oob" => $desktop_navbar,
                "mobile_navbar_oob" => $mobile_navbar,
            ]
        ));
    }

    public function logout_user(Request $_): Response
    {
        if (
            !isset($_SESSION[SESSION_USER_LOGGED_IN])
            && !$this->model->does_user_exists_and_active($_SESSION[SESSION_USER_EMAIL] ?? '')
        ) {
            return Response::forNotAllowed();
        }

        session_unset();
        session_destroy();
        return Response::forTemporaryRedirect($this->state->get_env('APP') ?? '/');
    }

    public function register_user(Request $request): Response
    {
        $params = $request->get_post_params();
        Utils::logDebug(var_export([$params, $_POST], true));
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
            || ($this->mail_html($email_to, $email_to_name, $email_body, 'Activate your Account') === false)
        ) {
            return Response::forFailedAction();
        }

        return new Response('All done. Please check your email inbox to activate your account');
    }

    public function initiate_password_reset(Request $request): Response
    {
        $params = $request->get_post_params();
        if (!isset($params['email'])) {
            return Response::forNotAllowed();
        }

        if (!$this->model->does_user_exists_and_active($params['email'])) {
            return Response::forNotFound();
        }
        $user_details = $this->model->get_active_user_details($params['email']);

        $magic_code = $this->gen_magic_code(ACTION::RESET_PASSWORD);
        $email_to = $user_details['email'];
        $email_to_name = $user_details['first_name'] . ' ' . $user_details['last_name'];
        $email_body = $this->render($this->state->VIEWS . 'email-user-reset-password', [
            'link' => $this->state->get_env('APP') . 'auth/continue-password-reset?code=' . $magic_code
        ]);
        if (
            ($this->magic_model->add_magic_code($magic_code, $params['email'], ACTION::RESET_PASSWORD) === false)
            || ($this->mail_html($email_to, $email_to_name, $email_body, 'Reset your password') === false)
        ) {
            return Response::forFailedAction();
        }
        return new Response('Check your inbox please');
    }

    public function complete_password_reset(Request $request): Response
    {
        $params = $request->get_post_params();
        $magic_code = $params['code'];
        if (!isset($params['new_password']) || !isset($magic_code)) {
            Utils::logDebug(var_export([
                $params, $magic_code
            ], true));
            return Response::forNotAllowed();
        }

        $magic_user = $this->magic_model->validate_magic_code($magic_code, ACTION::RESET_PASSWORD);
        if ($magic_user === false) {
            Utils::logInfo('AUTH_ERR: magic_transaction returned 404. code: ' . $magic_code);
            return Response::forNotFound();
        }

        if (!$this->model->reset_user_password($magic_user, $params['new_password'])) {
            return Response::forFailedAction();
        }

        return new Response('Password reset complete. Please login again manually.');
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

        return Response::forTemporaryRedirect($this->state->get_env('APP').'/auth/login');
    }
}



























