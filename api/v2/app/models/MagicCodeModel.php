<?php

namespace app\models;

use tinyfuse\BaseModel;
use tinyfuse\Utils;

class MagicCodeModel extends BaseModel
{
    public function add_magic_code(string $magic_code, string $email): bool
    {
        $sql = "INSERT INTO magiclinks(email, code, created_at) VALUE (?,?,?);";
        return gettype($this->execute($sql, [$email, $magic_code, Utils::get_time_full()])) === 'array';
    }

}