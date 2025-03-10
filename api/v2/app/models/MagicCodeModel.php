<?php

namespace app\models;

use tinyfuse\ACTION;
use tinyfuse\BaseModel;
use tinyfuse\Utils;

class MagicCodeModel extends BaseModel
{
    public function add_magic_code(string $magic_code, string $email, ACTION $action = ACTION::NEW_USER): bool
    {
        $sql = "INSERT INTO magiclinks(email, code, purpose, created_at) VALUE (?,?,?,?);";
        return $this->check_if_action_ok(
            $this->execute($sql, [$email, $magic_code, (int)$action->value, Utils::get_time_full()])
        );
    }

    public function validate_magic_code(string $magic_code, ACTION $action): string|false
    {
        $sql = "SELECT email, COUNT(*) as is_valid FROM magiclinks WHERE code=? AND purpose=? AND used=0 GROUP BY email;";
        $res = $this->execute($sql, [$magic_code, (int)$action->value])[0];
        return array_key_exists('is_valid', $res) && $res['is_valid'] === 1 ? $res['email'] : false;
    }

    public function complete_magic_transaction(string $magic_code): bool
    {
        $sql = "UPDATE magiclinks SET used=1 WHERE code=?";
        return $this->check_if_action_ok($this->execute($sql, [$magic_code]));
    }

}