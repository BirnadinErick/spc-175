<?php

namespace app\models;

use app\ProjectStatus;
use tinyfuse\BaseModel;
use tinyfuse\Utils;

class ProjectModel extends BaseModel
{

    public function get_all_project(): array
    {
        $sql = "SELECT id, title, status, amount, upvote, deadline, description FROM projects ORDER BY deadline;";
        return $this->execute($sql, []);
    }

    public function new_project(array $details): bool
    {
        $sql = "INSERT INTO projects (title, description, upvote, status, amount, deadline) VALUES(?,?,0,?,?,?);";
        return $this->check_if_action_ok(
            $this->execute($sql, [
                $details['title'],
                $details['description'],
                ProjectStatus::Pending->value,
                floatval($details['amount']),
                $details['deadline']
            ])
        );
    }

    public function get_project(int $project_id): array
    {
        $sql = "SELECT * FROM projects WHERE id = ?;";
        $res = $this->execute($sql, [$project_id]);
        return $res !== false ? $res[0] : [];
    }

    public function update_project(int $project_id, array $params): bool
    {
        $sql = "UPDATE projects SET title=?, description=?, status=?, amount=?, deadline=? WHERE id=?;";
        return $this->check_if_action_ok($this->execute($sql, [
            $params['title'],
            $params['description'],
            $params['status'],
            floatval($params['amount']),
            $params['deadline'],
            $project_id,
        ]));
    }
}