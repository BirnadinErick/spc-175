<?php

namespace tinyfuse\models;

use PHPMailer\PHPMailer\Exception;
use tinyfuse\lib\BaseModel;

require_once MODELS . "base.php";

class ContentsV2Model extends BaseModel
{
    private string $metatable = "contents_meta";

    private function sanitizeFilename(string $pathString, string $defaultExtension = 'html'): string|null
    {
        $filename = preg_replace('/[\/\\\\:*?"<>|]/', '_', $pathString);
        $filename = trim($filename, " .");

        if (empty($filename)) {
            return null;
        }

        if (!pathinfo($filename, PATHINFO_EXTENSION)) {
            $filename .= ".$defaultExtension";
        }
        return substr($filename, 0, 255);
    }

    private function write_raw_FS(string $path, string $data): bool
    {
        $filepath = $this->sanitizeFilename($path, "json");
        return $this->write_FS("contents/raw/$filepath", $data);
    }

    private function write_html_fs(string $path, string $data): bool
    {
        $filepath = $this->sanitizeFilename($path, "html");
        return $this->write_FS("contents/$filepath", $data);
    }

    private function write_FS(string $filePath, string $data): bool
    {
        if (file_put_contents($filePath, $data) === false) {
            debug("write_FS failed: $filePath.", __FILE__);
            return false;
        }

        return true;
    }

    public function get_rendered_content(string $path): string|null
    {
        debug("get_rendered_content: $path", __FILE__);

        if (!file_exists($path)) {
            debug("get_rendered_content: $path 404", __FILE__);
            return null;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            debug("get_rendered_content: $path failed to read.", __FILE__);
            return null;
        }

        return $content;
    }

    public function get_raw(string $path): string|null
    {
        $file = 'contents/raw/' . $this->sanitizeFilename($path, 'json');
        if (!file_exists($file)) {
            debug("$file 404", __FILE__);
            return null;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            debug("get_raw: $path failed to read.", __FILE__);
            return null;
        }

        return $content;
    }

    private function check_if_content_exists(string $path): bool|int
    {
        try {
            $checkSql = "SELECT id FROM $this->metatable WHERE path = ?";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([$path]);
            return $checkStmt->fetchColumn();

        }catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    public function update(string $path, int $user_id, string $data_raw, string $data_rendered): bool
    {
        debug("updating $path", __FILE__);
        $meta_id = $this->check_if_content_exists($path);
        if ($meta_id === false || $meta_id <= 0) {
            debug("$path 404 | id: $meta_id", __FILE__);
            return false;
        }

        $ok_raw = $this->write_raw_FS($path, $data_raw);
        $ok_html = $this->write_html_fs($path, $data_rendered);
        if (!$ok_html || !$ok_raw) {
            debug("failed to update: $path", __FILE__);
            return false;
        }

        try {
            $sql = "UPDATE contents_meta SET updated_by=?, updated_at=? WHERE id=?;";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$user_id, current_time(), $meta_id]);
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    public function delete(string $path): bool
    {
        if (!$this->check_if_content_exists($path)){
            return false;
        }

        try {
            $sql = "DELETE FROM contents_meta WHERE `path`=?;";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$path]);
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

}