<?php

namespace tinyfuse;

use Error;
use mysqli;

class BaseModel
{
    private mysqli $conn;

    function __construct(BaseState $state)
    {
        $host = $state->get_env('DB_HOST');
        $db = $state->get_env('DB_NAME');
        $username = $state->get_env('DB_USERNAME');
        $password = $state->get_env('DB_PASSWORD');

        $this->conn = new mysqli($host, $username, $password, $db);
        if ($this->conn->connect_error) {
            // no way, we can move forward, we exit here.
            $state->LOG_LEVEL === LOG_LEVEL::DEBUG
                ? Utils::logDebug("DB_VARS: $host\t$db\t$username\t$password")
                : Utils::logInfo('DB failed to connect');
            exit(1);
        }
    }

    private function construct_params(array $params): array
    {
        $types = "";
        $values = [];

        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= "i";
            } elseif (is_double($param)) {
                $types .= "d";
            } else {
                $types .= "s";
            }
            $values[] = $param;
        }

        return [$types, $values];
    }

    protected function execute(string $statement, array $params): array|Error
    {
        $stmt = $this->conn->prepare($statement);

        if (!$stmt) {
            Utils::logInfo("DB_STMT: " . $this->conn->error);
        }

        if (!empty($params)) {
            [$types, $values] = $this->construct_params($params);
            $stmt->bind_param($types, ...$values);
        }

        $stmt->execute();

        $result = $stmt->get_result();
        if ($result) {
            $data = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $data = [];
        }

        $stmt->close();
        return $data;
    }
}