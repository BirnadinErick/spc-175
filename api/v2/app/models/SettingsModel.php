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

}