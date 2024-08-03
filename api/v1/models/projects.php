<?php

require_once MODELS . "base.php";

class ProjectsModel extends BaseModel
{
    protected string $tableName = "projects";

    public function addProject(string $title, string $desc, float $amount, string $deadline): bool
    {
        $sql =
            "INSERT INTO " .
            $this->tableName .
            " (title, description, upvote, status, amount, deadline) 
        VALUES (:title, :description, :upvote, :status, :amount, :deadline)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":title", $title);
            $stmt->bindValue(":description", $desc);
            $stmt->bindValue(":upvote", 0);
            $stmt->bindValue(":status", "requested");
            $stmt->bindValue(":amount", $amount);
            $stmt->bindValue(":deadline", $deadline);
            $stmt->execute();

            debug(
                "New project '$title' is inserted!",
                __FILE__
            );
            return true;
        } catch (PDOException $e) {
            // Log the failure
            $stdout = fopen("php://stdout", "w");
            fwrite($stdout, $e);
            fclose($stdout);

            return false;
        }
    }

    public function getProjects()
    {
        // TODO: Paginate
        $sql = "SELECT id, title, status, amount, upvote, deadline FROM " . $this->tableName;

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result) {
                return $result;
            }
            return -1;

        } catch (PDOException $e) {
            // Log the failure
            $stdout = fopen("php://stdout", "w");
            fwrite($stdout, $e);
            fclose($stdout);
        }

        return -1;
    }

    public function getProject(string $project_id)
    {
        $sql = "SELECT * FROM projects WHERE id = :project_id";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":project_id", $project_id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                return $result;
            }
            return -1;

        } catch (PDOException $e) {
            // Log the failure
            $stdout = fopen("php://stdout", "w");
            fwrite($stdout, $e);
            fclose($stdout);
        }

        return -1;
    }

    public function getProjectComments(string $project_id): int|array
    {
        $sql = "SELECT u.first_name as fname, u.last_name as lname, c.`text` as comment from projects_comments pc join comments c on c.id = pc.comment_id join users u on u.id = c.user_id where pc.project_id = :pid;";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":pid", $project_id);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            debug(var_export($result, true), __FILE__);
            if ($result) {
                return $result;
            }
            return -1;

        } catch (PDOException $e) {
            // Log the failure
            $stdout = fopen("php://stdout", "w");
            fwrite($stdout, $e);
            fclose($stdout);
        }

        return -1;
    }
}
