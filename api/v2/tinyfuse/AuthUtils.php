<?php

namespace tinyfuse;

trait AuthUtils
{
    protected function is_anon_user(): bool
    {
        return !isset($_SESSION[SESSION_USER_LOGGED_IN]);
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