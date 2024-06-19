<?php

require_once MODELS."base.php";

class ContentsModel extends BaseModel
{
    function write_content($path, $uid, $updated_by, $data) {
        $sql = 'INSERT INTO contents (path, uid, updated_by, data) VALUES (?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$path, $uid, $updated_by, $data]);
    }

    function update_content($path, $uid, $updated_by, $data) {
        try {
            // for now let's do this instead of using JOIN to check existence to
            // keep the code compatible with both SQLite/MySQL/PostgresSQL
            $checkSql = 'SELECT COUNT(*) FROM contents WHERE path = ?';
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([$path]);
            $recordExists = $checkStmt->fetchColumn();

            if ($recordExists) {
                debug("content found, updating...", __FILE__);
                $updateSql = 'UPDATE contents SET uid = ?, updated_by = ?, data = ?, updated_at = time("now") WHERE path = ?';
                $updateStmt = $this->pdo->prepare($updateSql);
                $updateStmt->execute([$uid, $updated_by, $data, $path]);

                return 0;
            } else {
                debug("non-existent content", __FILE__);
                return -1;
            }
        } catch (PDOException $e) {
            debug($e->getMessage(), __FILE__);
        }

        return -1;
    }


    function read_content(string $path) {
        $sql = 'SELECT * FROM contents WHERE path = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$path]);
        return $stmt->fetch();
    }
}