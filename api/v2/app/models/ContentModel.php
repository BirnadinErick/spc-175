<?php

namespace app\models;

use tinyfuse\BaseModel;
use tinyfuse\BaseState;

class ContentModel extends BaseModel
{
    public function __construct(BaseState $state)
    {
        parent::__construct($state);
    }

    public function get_content_html_file(string $slug):string|null
    {
        $sql = "SELECT html_file_name as file FROM contents_meta WHERE path = ?;";
        $res = $this->execute($sql, [$slug]);
        return $res !== false ? $res[0]['file'] : null;
    }

    public function get_editable_meta(): array|null
    {
        $sql = "SELECT path, updated_at as time FROM spc.contents_meta ORDER BY path;";
        $res = $this->execute($sql, []);
        return $res !== false ? $res : null;
    }
}