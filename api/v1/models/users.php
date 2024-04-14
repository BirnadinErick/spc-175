<?php
require_once(MODELS . "base.php");

class UsersModel extends BaseModel
{

    protected string $tableName = 'users';

    public function authenticateUser(string $username, string $password): bool
    {
        $sql = "SELECT * FROM users WHERE email = :username AND password = :password";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // log the failure
            $stdout = fopen('php://stdout', 'w');
            fwrite($stdout, $e);
            fclose($stdout);

            return false;
        }
    }

    public function addNewUser($data): bool
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO $this->tableName ($columns) VALUES ($placeholders)";

        try {
            $stmt = $this->pdo->prepare($sql);

            $i = 1;
            foreach ($data as $value) {
                $stmt->bindValue($i++, $value);
            }

            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            // log the failure
            $stdout = fopen('php://stdout', 'w');
            fwrite($stdout, $e);
            fclose($stdout);

            return false;
        }
    }
}
