<?php

namespace tinyfuse\lib;

use PDO;

class Settings
{
    private static PDO|null $pdo = null;

    private static function connect(): ?PDO
    {
        if (self::$pdo === null) {
            $host = $_ENV['DB_HOST'];
            $db = $_ENV['DB_NAME'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];
            $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

            self::$pdo = new PDO($dsn, $username, $password);
        }
        return self::$pdo;
    }

    public static function set($key, $value): bool
    {
        $pdo = self::connect();

        $stmt = $pdo->prepare("
            INSERT INTO settings (`key`, `value`) 
            VALUES (:key, :value)
            ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)
        ");

        return $stmt->execute([
            ':key' => $key,
            ':value' => $value
        ]);
    }

    public static function get($key): string|null
    {
        $pdo = self::connect();

        $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = :key");
        $stmt->execute([':key' => $key]);

        $result = $stmt->fetchColumn();
        return $result !== false ? $result : null;
    }
}


