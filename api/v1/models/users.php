<?php
require_once(MODELS . "base.php");

class UsersModel extends BaseModel
{

    protected string $tableName = 'users';

    private function hash_password($password): string
    {
        // omitted PASSWORD_DEFAULT to eliminate data inconsistency in datastore
        // since if newer better algo is found with different length, then data
        // will start to incur tech-debt
        return password_hash($password, PASSWORD_BCRYPT);
    }

    private function verify_password(string $password, string $hash): bool
    {
        $ok = password_verify($password, $hash);

        if ($ok) {
            return true;
        } else {
            return false;
        }
    }

    public function authenticateUser(string $username, string $password): bool
    {
        $sql = "SELECT * FROM users WHERE email = :username";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $this->verify_password($password, $result['password']);
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

        // prepare password
        $data['password'] = $this->hash_password($data['password']);

        try {
            $stmt = $this->pdo->prepare($sql);

            $i = 1;
            foreach ($data as $value) {
                $stmt->bindValue($i++, $value);
            }

            debug("new user query: $sql", __FILE__);
            $stmt->execute();

            debug("new user!", __FILE__);
            return true;
        } catch (PDOException $e) {
            // log the failure
            $stdout = fopen('php://stdout', 'w');
            fwrite($stdout, $e);
            fclose($stdout);

            debug("failed new user! $e", __FILE__);
            return false;
        }
    }
}
