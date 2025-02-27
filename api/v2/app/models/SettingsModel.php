<?php

namespace app\models;

use tinyfuse\BaseModel;

class SettingsModel extends BaseModel
{
    public function get_count(): int
    {
        $sql = "SELECT `value` as count FROM settings WHERE `key`= ?;";
        $res = $this->execute(statement: $sql, params: ["count"]);
        return $res[0]['count'];
    }

    public function set_count(int $value): int|null
    {
        $sql = "UPDATE settings SET value=? WHERE `key`='count';";
        $res = $this->execute($sql, [$value]);
        return $res !== false ? $value : null;
    }

}