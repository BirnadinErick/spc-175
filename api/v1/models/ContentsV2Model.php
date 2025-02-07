<?php

namespace tinyfuse\models;

use PHPMailer\PHPMailer\Exception;
use tinyfuse\lib\BaseModel;
use tinyfuse\lib\contentengine\ContentEngine;

require_once MODELS . "base.php";
require_once APP . 'lib/contentengine/ContentEngine.php';

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

    private function write_html_FS(string $path, string $data): bool
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

        $path = 'contents/' . $this->sanitizeFilename($path, "html");
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

        } catch (Exception $e) {
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
        $ok_html = $this->write_html_FS($path, $data_rendered);
        if (!$ok_html || !$ok_raw) {
            debug("failed to update: $path", __FILE__);
            return false;
        }

        try {
            $sql = "UPDATE $this->metatable SET updated_by=?, updated_at=? WHERE id=?;";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$user_id, current_time(), $meta_id]);
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    public function delete(string $path): bool
    {
        if (!$this->check_if_content_exists($path)) {
            return false;
        }

        try {
            $file_raw = "contents/raw/" . $this->sanitizeFilename($path, "json");
            $file_html = "contents/" . $this->sanitizeFilename($path, "html");
            $sql = "DELETE FROM $this->metatable WHERE `path`=?;";
            $stmt = $this->pdo->prepare($sql);

            if (!$stmt->execute([$path]) || !unlink($file_html) || !unlink($file_raw)) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    public function migrate_metadata(): bool
    {
        $old_contents = "SELECT DISTINCT path, updated_by, updated_at, data FROM contents;";
        $old_contents = $this->pdo->prepare($old_contents);
        $old_contents->execute();
        $old_contents = $old_contents->fetchAll();

        $ce = new ContentEngine();
        foreach ($old_contents as $oc) {
            $data = bzdecompress($oc['data']);

            // write the data
            $this->write_raw_FS($oc['path'], $data);
            $this->write_html_FS($oc['path'], $ce->render($data));

            // write metadata
            $path = $oc['path'];
            $file_raw = $this->sanitizeFilename($path, "json");
            $file_html = $this->sanitizeFilename($path, "html");
            $sql = "INSERT INTO $this->metatable (`path`, raw_file_name, html_file_name, updated_by, updated_at) VALUES(?,?,?,?,?);";
            $stmt = $this->pdo->prepare($sql);
            if (!$stmt->execute([$path, $file_raw, $file_html, $oc['updated_by'], $oc['updated_at']])) {
                debug("failed to migrate: $path", __FILE__);
                return false;
            }
        }

        return true;
    }
}