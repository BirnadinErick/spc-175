<?php

namespace tinyfuse;

use app\models\AuthModel;

trait AuthUtils
{
    protected function is_anon_user(): bool
    {
        return !isset($_SESSION[SESSION_USER_LOGGED_IN]);
    }

    protected function get_user_id(BaseState $state): int
    {
        if ($this->is_anon_user()) {
            return -2003;
        }
        $auth_model = new AuthModel($state);
        return $auth_model->get_user_id($_SESSION[SESSION_USER_EMAIL] ?? '');
    }

    protected function get_user_role(): int
    {
        return isset($_SESSION[SESSION_USER_ROLE]) ? intval($_SESSION[SESSION_USER_ROLE]) : 0;
    }

    protected function get_user_email(): string|null
    {
        return (
            isset($_SESSION[SESSION_USER_LOGGED_IN])
            && $_SESSION[SESSION_USER_LOGGED_IN] === '1'
            && isset($_SESSION[SESSION_USER_EMAIL])
        )
            ? $_SESSION[SESSION_USER_EMAIL] : null;
    }

}