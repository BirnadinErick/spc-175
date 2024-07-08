<?php

use BumpCore\EditorPhp\Helpers;

require_once MODELS . "base.php";

class ContentsModel extends BaseModel
{
    function write_content($path, $uid, $updated_by, $data, $meta = null): bool
    {
        $sql = 'INSERT INTO contents (path, uid, updated_by, data, meta, updated_at) VALUES (?, ?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$path, $uid, $updated_by, $data, $meta, date('Y-m-d')]);
    }

    function update_content($path, $updated_by, $data): int
    {
        try {
            // for now let's do this instead of using JOIN to check existence to
            // keep the code compatible with both SQLite/MySQL/PostgresSQL
            $checkSql = 'SELECT COUNT(*) FROM contents WHERE path = ?';
            $checkStmt = $this->pdo->prepare($checkSql);
            $checkStmt->execute([$path]);
            $recordExists = $checkStmt->fetchColumn();

            if ($recordExists) {
                debug("content found, updating...", __FILE__);
                $updateSql = 'UPDATE contents SET updated_by = ?, data = ?, updated_at = ? WHERE path = ?';
                $updateStmt = $this->pdo->prepare($updateSql);
                $date = date('Y-m-d');
                $updateStmt->execute([$updated_by, $data, $date, $path]);

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

    // from https://stackoverflow.com/questions/2955251/php-function-to-make-slug-url-string
    static public function generate_slug($text): string
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

    function read_content(string $path)
    {
        debug("ContentsModel received $path", __FILE__);
        $sql = 'SELECT * FROM contents WHERE path = ? OR path = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$path, substr($path, 0, strlen($path) - 1)]);
        return $stmt->fetch();
    }

    public function get_contents()
    {
        $sql = 'SELECT DISTINCT path FROM contents';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function get_feat(): array
    {
        /*
        we don't waste resource searching for last `feat` by searching
        meta string. Instead, we only take first 10 and search if any of
        them are `feat`.

        If not, then we pass and return the latest entry. Cuz nobody knows
        which is `feat` and which is not. We play God here!*/

        debug("getting feat", __FILE__);
        $sql = 'SELECT updated_at, path, meta FROM contents ORDER BY updated_at DESC, id DESC LIMIT 10';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // we need to parse 'meta'
        foreach ($blogs as &$b) {
            $meta = $b['meta'];
            $meta = json_decode($meta, true);
            $b['meta'] = $meta;
        }
        unset($b);

        /*
         * filter for feats.
         *
         * since arrays are ordered at our SQL query guaranteed to bring
         * latest blog before oldest, we can go in turns and assume that
         * the first discovery is latest.
         *
         * If no discovery is found then we return the first item from the
         * datastore.
         */
        foreach ($blogs as $b) {
            if (str_contains($b['meta']['tags'], 'feat')) {
                return $b;
            }
        }

        // return first result if no `feat` tag was discovered.
        return $blogs[0];
    }

    public function get_latest_blogs() {
        $sql = "SELECT updated_at, path, meta FROM contents WHERE meta != '' AND meta IS NOT NULL ORDER BY updated_at DESC LIMIT 10";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $output = [];
        foreach ($blogs as $b) {
            $meta = $b['meta'];
            $meta = json_decode($meta, true);
            $output[] = [
                "cover" => $meta['cover'],
                "title"=>$meta['title'],
                "href"=>$b['path'],
                "date"=>$b["updated_at"]
            ];
        }

        return $output;
    }

    public function get_blogs()
    {
        $sql = "SELECT updated_at, path, meta FROM contents WHERE meta != '' AND meta IS NOT NULL ORDER BY updated_at DESC LIMIT 100";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $output = '';
        foreach ($blogs as $b) {
            $meta = $b['meta'];
            $meta = json_decode($meta, true);
            $b['meta'] = $meta;
//            debug(var_export($b, true), __FILE__);
            $output .= Helpers::renderNative(VIEWS . 'blog-list-single.php', [
                'path' => $b['path'],
                'cover' => $b['meta']['cover'],
                'title' => $b['meta']['title'],
                'date' => $b['updated_at'],
                'desc' => str_replace(',', ', ', $b['meta']['tags'])
            ]);
        }

        echo $output;
        exit(0);
    }
}