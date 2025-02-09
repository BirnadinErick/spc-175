<?php

namespace tinyfuse\models;

use Exception;
use tinyfuse\lib\BaseModel;

class ProjectsModelV2 extends BaseModel
{
    private string $comments_table = 'projectcomments';
    private string $projects_table = 'projects';

    public function create_comment(string $project, mixed $comment, int $user): bool
    {
        $sql = "INSERT INTO projectcomments (project_id, comment, user_id) VALUES(?,?,?);";
        return $this->try_execute($sql, [$project, $comment, $user]);
    }

    public function get_comments(int $project_id): array|false
    {
        $sql = "SELECT p.comment, p.created_at , u.first_name as fname , u.last_name as lname FROM projectcomments p JOIN users u ON p.user_id = u.id WHERE p.project_id = ?;";
        return $this->try_fetch_all($sql, [$project_id]);
    }

}