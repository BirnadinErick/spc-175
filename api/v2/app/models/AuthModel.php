<?php

namespace app\models;

use tinyfuse\BaseModel;
use tinyfuse\CryptoFunctions;
use tinyfuse\UserRole;
use tinyfuse\Utils;

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
            (int)$params['year_of_batch'],
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

    public function does_user_exists_and_active(string $email): bool
    {
        $sql = "SELECT COUNT(*) as is_user_ok FROM users WHERE email = ? AND isactive = 1;";
        $res = $this->execute($sql, [$email])[0];
        return array_key_exists('is_user_ok', $res) && $res['is_user_ok'] === 1;
    }

    public function get_active_user_details(string $email): array
    {
        $sql = "SELECT * FROM users WHERE email = ? AND isactive=1;";
        return $this->execute($sql, [$email])[0];
    }

    public function is_user_cred_valid(string $password, string $email): bool
    {
        $sql = "SELECT password FROM users WHERE email = ? AND isactive = 1;";
        $res = $this->execute($sql, [$email])[0];
        return array_key_exists('password', $res) && password_verify($password, $res['password']);
    }

    public function get_user_role(string $email): int
    {
        $sql = "SELECT role FROM users WHERE email = ? AND isactive = 1;";
        $res = $this->execute($sql, [$email])[0];
        return $res['role'] ?? -2003;
    }

    public function activate_user(string $email): bool
    {
        $sql = "UPDATE users SET isactive = 1 WHERE email = ? AND isactive =0;";
        return $this->check_if_action_ok($this->execute($sql, [$email]));
    }

    public function reset_user_password(string $email, string $new_password_txt): bool
    {
        $new_password_hash = $this->hash_password($new_password_txt);
        $sql = "UPDATE users SET password = ? WHERE email = ? AND isactive=1;";
        return $this->check_if_action_ok($this->execute($sql, [$new_password_hash, $email]));
    }

    public function change_user_role(string $email, int $new_role): bool
    {
        $sql = "UPDATE users SET role = ? WHERE email = ?;";
        return $this->check_if_action_ok($this->execute($sql, [$new_role, $email]));
    }

    public function get_user_display_name(string $email): string
    {
        if ($email === '') {
            return 'Patrician';
        }

        $sql = "SELECT first_name, last_name FROM users WHERE email = ? AND isactive = 1;";
        $res = $this->execute($sql, [$email])[0];
        return ucfirst(strtolower($res['first_name'])) . ' ' . strtoupper(substr($res['last_name'], offset: 0, length: 1));
    }

    public function get_user_id(string $email): int
    {
        $sql = 'SELECT id FROM users WHERE email = ? AND isactive = 1;';
        $res = $this->execute($sql, [$email]);
        return $res !== false ? $res[0]['id'] : -2003;
    }
}