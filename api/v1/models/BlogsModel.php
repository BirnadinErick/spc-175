<?php

namespace tinyfuse\models;

require_once APP . 'lib/BaseModel.php';
require_once APP . 'lib/contentengine/ContentEngine.php';

use Exception;
use PDO;
use tinyfuse\lib\BaseModel;
use tinyfuse\lib\contentengine\ContentEngine;
use tinyfuse\lib\Settings;

class BlogsModel extends BaseModel
{
    private string $metatable = 'blogs_meta';

    // from https://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
    private function generate_slug($text): string
    {
        $divider = "-";

        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function get_editable_contents(): array|false
    {
        try {
            $sql = "SELECT title, slug, updated_at as time FROM $this->metatable ORDER BY id DESC;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    public function delete(string $slug): bool
    {
        if (!$this->check_if_blog_exists($slug)) {
            return false;
        }

        try {
            $file_raw = "blogs/raw/" . $this->sanitizeFilename($slug, "json");
            $file_html = "blogs/" . $this->sanitizeFilename($slug, "html");
            $sql = "DELETE FROM $this->metatable WHERE slug=?;";
            $stmt = $this->pdo->prepare($sql);

            if (!$stmt->execute([$slug]) || !unlink($file_html) || !unlink($file_raw)) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    private function check_if_blog_exists(string $slug): bool|int
    {
        try {
            $checkSql = "SELECT id FROM $this->metatable WHERE slug = ?";
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([$slug]);
            return $checkStmt->fetchColumn();
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    private function sanitizeFilename(string $pathString, string $file_extension = 'html'): string|null
    {
        $filename = preg_replace('/[\/\\\\:*?"<>|]/', '_', $pathString);
        $filename = trim($filename, " .");

        if (empty($filename)) {
            return null;
        }

        if (!pathinfo($filename, PATHINFO_EXTENSION)) {
            $filename .= ".$file_extension";
        }
        return substr($filename, 0, 255);
    }

    private function write_raw_FS(string $path, string $data): bool
    {
        $filepath = $this->sanitizeFilename($path, "json");
        return $this->write_FS("blogs/raw/$filepath", $data);
    }

    private function write_html_FS(string $path, string $data): bool
    {
        $filepath = $this->sanitizeFilename($path, "html");
        return $this->write_FS("blogs/$filepath", $data);
    }

    private function write_FS(string $filePath, string $data): bool
    {
        if (file_put_contents($filePath, $data) === false) {
            debug("write_FS failed: $filePath.", __FILE__);
            return false;
        }

        return true;
    }

    public function new_blog(string $title, string $data, string $tags, string $cover, int $user_id): string|false
    {
        $ce = new ContentEngine();
        $slug = $this->generate_slug($title);
        $ok_raw = $this->write_raw_FS($slug, $data);
        $ok_html = $this->write_html_FS($slug, $ce->render($data));

        if (!$ok_raw || !$ok_html) {
            debug("new_blog: writing to FS failed", __FILE__);
            return false;
        }

        try {
            $file_raw = $this->sanitizeFilename($slug, "json");
            $file_html = $this->sanitizeFilename($slug, "html");

            $sql = "INSERT INTO blogs_meta (slug, raw_file_name, html_file_name, updated_by, updated_at, title, tags, cover) VALUES(?,?,?,?,?,?,?, ?);";
            $stmt = $this->pdo->prepare($sql);
            $ok = $stmt->execute([$slug, $file_raw, $file_html, $user_id, current_time(), $title, $tags, $cover]);

            if ($ok) {
                return $slug;
            } else {
                return false;
            }
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    public function get_raw(string $slug): string|null
    {
        $file = 'blogs/raw/' . $this->sanitizeFilename($slug, 'json');
        if (!file_exists($file)) {
            debug("$file 404", __FILE__);
            return null;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            debug("get_raw: $slug failed to read.", __FILE__);
            return null;
        }

        return $content;
    }

    public function update(string $slug, int $user_id, string $data_raw, string $data_rendered): bool
    {
        debug("updating $slug", __FILE__);
        $meta_id = $this->check_if_blog_exists($slug);
        if ($meta_id === false || $meta_id <= 0) {
            debug("$slug 404 | id: $meta_id", __FILE__);
            return false;
        }

        $ok_raw = $this->write_raw_FS($slug, $data_raw);
        $ok_html = $this->write_html_FS($slug, $data_rendered);
        if (!$ok_html || !$ok_raw) {
            debug("failed to update: $slug", __FILE__);
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

    public function get_feat(): array|false
    {
        $feat_slug = Settings::get(SETTINGS_BLOG_FEAT_KEY);
        try {
            $sql = "SELECT title, cover, updated_at, slug FROM blogs_meta WHERE slug = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$feat_slug]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            debug($e->getMessage(), __FILE__);
            return false;
        }
    }

    public function set_feat(string $slug): bool
    {
        return Settings::set(SETTINGS_BLOG_FEAT_KEY, $slug);
    }
}