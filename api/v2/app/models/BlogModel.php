<?php

namespace app\models;

use tinyfuse\BaseModel;
use tinyfuse\BaseState;
use tinyfuse\Utils;

class BlogModel extends BaseModel
{
    public function __construct(BaseState $state)
    {
        parent::__construct($state);
    }

    public function get_content_html_file(string $slug): string|null
    {
        $sql = "SELECT html_file_name as file FROM contents_meta WHERE path = ?;";
        $res = $this->execute($sql, [$slug]);
        return $res !== false ? $res[0]['file'] : null;
    }

    public function get_editable_meta(): array|null
    {
        $sql = "SELECT slug, updated_at as time, title FROM spc.blogs_meta ORDER BY updated_at;";
        $res = $this->execute($sql, []);
        return $res !== false ? $res : null;
    }

    public function get_content_raw_file(string $slug): string|null
    {
        $sql = "SELECT contents_meta.raw_file_name as file FROM contents_meta WHERE path = ?;";
        $res = $this->execute($sql, [$slug]);
        return $res !== false ? $res[0]['file'] : null;
    }

    private function does_content_exist(string $slug): int|false
    {
        $sql = "SELECT id FROM spc.contents_meta WHERE path = ?";
        $res = $this->execute($sql, [$slug]);

        return $res !== false ? $res[0]['id'] : false;
    }

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
        return $this->write_FS($this->state->VIEWS . "contents/raw/$filepath", $data);
    }

    private function write_html_FS(string $path, string $data): bool
    {
        $filepath = $this->sanitizeFilename($path, "html");
        return $this->write_FS($this->state->VIEWS . "contents/$filepath", $data);
    }

    private function write_FS(string $filePath, string $data): bool
    {
        if (file_put_contents($filePath, $data) === false) {
            Utils::logInfo(
                "CONTENT_ERR: failed to write $filePath!"
            );
            return false;
        }

        return true;
    }

    public function update_content(string $slug, int $editor_id, string $data_raw, string $data_rendered): bool
    {
        $meta_id = $this->does_content_exist($slug);
        if ($meta_id === false) {
            Utils::logInfo("CONTENT_ERR: failed to find metadata for content-path: $slug");
            return false;
        }

        $ok_raw = $this->write_raw_FS($slug, $data_raw);
        $ok_html = $this->write_html_FS($slug, $data_rendered);
        if (!$ok_html || !$ok_raw) {
            Utils::logInfo("CONTENT_ERR: failed to write update content($slug) to either raw/rendered");
            return false;
        }

        $sql = "UPDATE contents_meta SET updated_by=?, updated_at=? WHERE id=?;";
        $res = $this->execute($sql, [
            $editor_id, Utils::get_time_full(), $meta_id
        ]);

        return $res !== false;
    }
}



























