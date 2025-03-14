<?php

namespace app\models;

use app\ProjectStatus;
use tinyfuse\BaseModel;

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
}