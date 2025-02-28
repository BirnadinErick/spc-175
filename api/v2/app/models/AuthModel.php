<?php

namespace app\models;

use tinyfuse\BaseModel;
use tinyfuse\CryptoFunctions;
use tinyfuse\UserRole;

class AuthModel extends BaseModel
{

    use CryptoFunctions;

    public function new_user(array $params): bool
    {
        $params = [
            $params['first_name'],
            $params['last_name'],
            $params['email'],
            $this->hash_password($params['password']),
            (int) $params['year_of_batch'],
            $params['country'],
            $params['address_line_1'],
            $params['address_line_2'] ?? null,
            $params['zip_code'],
            $params['telephone'],
            UserRole::VISITOR->value,
            0
        ];
        $sql = "INSERT INTO users(
first_name, last_name, email, password, year_of_batch, country, address_line_1, address_line_2, zip_code, telephone, role, isactive
    ) VALUE (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

        return !($this->execute($sql, $params) === false);
    }
}