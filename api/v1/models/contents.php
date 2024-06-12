<?php

require_once MODELS."base.php";

class ContentsModel extends BaseModel
{
    function write_content($path, $uid, $updated_by, $data) {
        $sql = 'INSERT INTO contents (path, uid, updated_by, data) VALUES (?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$path, $uid, $updated_by, $data]);
    }

    function read_content(string $path) {
        $sql = 'SELECT * FROM contents WHERE path = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$path]);
        return $stmt->fetch();
    }
}