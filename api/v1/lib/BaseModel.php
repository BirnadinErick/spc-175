<?php

namespace tinyfuse\lib;

use Exception;
use PDO;

class BaseModel
{
    protected string $dsn = 'mysql:';
    protected string $username = '';
    protected string $password = '';

    protected PDO $pdo;

    function __construct()
    {
        // TODO: remove after porting
        return;
        $host = $_ENV['DB_HOST'];
        $db = $_ENV['DB_NAME'];

        $this->dsn .= "host=$host;dbname=$db;charset=utf8mb4";
        $this->username = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASS'];

        $this->pdo = new PDO($this->dsn, $this->username, $this->password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }

    public function try_fetch_all(string $sql, array $values): array|false
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    public function try_execute(string $sql, array $values): bool
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($values);
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    public function try_fetch_col(string $sql, array $values): mixed
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($values);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }
}
