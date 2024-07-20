<?php

namespace tinyfuse\models;

require_once APP."lib/BaseModel.php";

use PDO;
use PDOException;
use tinyfuse\lib\BaseModel;
use tinyfuse\lib\Constants;

class UsersModel extends BaseModel
{
    private static function hash_password(string $password): string
    {
        // omitted PASSWORD_DEFAULT to eliminate data inconsistency in datastore
        // since if newer better algo is found with different length, then data
        // will start to incur tech-debt
        return password_hash($password, PASSWORD_BCRYPT);

    }

    private static function verify_password(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * @param string $email
     * @return int|Constants
     *
     * return integer can be checked with static properties
     * like NotFound or InternalError
     */
    public function get_user_id(string $email): int|Constants
    {
        $sql = 'SELECT id FROM users WHERE email = :email';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result["id"];
            }

            return Constants::NotFound;
        } catch (PDOException $e) {
            debug($e->getMessage(), __FILE__);
            return Constants::InternalError;
        }
    }

    /**
     * returns role of a user object associated with
     * given user id
     * @param int $user_id
     * @return int
     */
    private function get_user_role(int $user_id): int
    {
        $stmt = $this->pdo->prepare('SELECT role FROM users WHERE id = ?');
        $stmt->execute([$user_id]);

        return $stmt->fetchColumn();
    }

    /**
     * given email in datastore and reference role, returns whether
     * user record associated with the email has concerning reference
     * role assigned to it.
     * @param int $ref_roles
     * @param string $email
     * @return bool
     */
    public function check_roles_exist(int $ref_roles, string $email): bool
    {
        switch ($id = $this->get_user_id($email)) {
            case Constants::InternalError:
            case Constants::NotFound:
                return false;
            default:
                $user_roles = $this->get_user_role($id);
                return ($user_roles & $ref_roles) === $ref_roles;
        }
    }

    /**
     * returns decorated name; i.e. capitalized first name and
     * initial of uppercase lastname with succeeding period.
     * @param string $email
     * @return string
     * @example Birnadin Erick -> Birnadin E.
     */
    public function get_decorated_name(string $email): string
    {
        $stmt = $this->pdo->prepare('SELECT first_name, last_name FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $data = $stmt->fetch();

        return ucfirst($data['first_name']) . ' ' . ucfirst($data['last_name'])[0] . '.';
    }

    /**
     * authenticate a session with given credentials
     * @param string $email
     * @param string $password
     * @return Constants
     */
    public function authenticateUser(string $email, string $password): Constants
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && $this::verify_password($password, $result["password"])) {
                return Constants::OK;
            } else {
                return Constants::NotFound;
            }
        } catch (PDOException $e) {
            debug($e->getMessage(), __FILE__);
            return Constants::InternalError;
        }
    }

    /**
     * insert new user data to datastore
     * @param array $data
     * @return bool
     */
    public function addNewUser(array $data): bool
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = 'INSERT INTO users ($columns) VALUES ($placeholders)';

        $data["password"] = $this::hash_password($data["password"]);

        try {
            $stmt = $this->pdo->prepare($sql);

            $i = 1;
            foreach ($data as $value) {
                $stmt->bindValue($i++, $value);
            }

            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            debug("failed new user! $e", __FILE__);
            return false;
        }
    }
}