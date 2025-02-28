<?php

namespace tinyfuse;

trait CryptoFunctions
{
    protected function gen_magic_code(ACTION $action): string
    {
        $delim = array_key_exists('MAGIC_DELIM', $_ENV) ? $_ENV['MAGIC_DELIM'] : '::';
        return match ($action) {
            ACTION::NEW_USER => ($_ENV['magic_code'] ?? 'spcmediaunit2023') . $delim . ('new_user'),
            default => $_ENV['magic_code'] ?? 'spcmediaunit2023',
        };
    }

    protected function hash_password(string $txt): string
    {
        return password_hash($txt, PASSWORD_BCRYPT);
    }

}