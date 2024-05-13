<?php

require_once MODELS . "base.php";

class CommentsModel extends BaseModel
{
    protected string $tableName = "comments";

    public function addComment(int $post_id, string $text, int $user_id): bool
    {
        $sql =
            "INSERT INTO " .
            $this->tableName .
            " (text, user_id, parent_id, is_reply, date_created, upvotes) 
        VALUES (:text, :user_id, :parent_id, :is_reply, :date_created, :upvotes)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":text", $text);
            $stmt->bindValue(":user_id", $user_id);
            $stmt->bindValue(":parent_id", $post_id);
            // since this is a comment, features of a reply is set to NULL
            $stmt->bindValue(":is_reply", 0);
            $stmt->bindValue(":date_created", time());
            $stmt->bindValue(":upvotes", 0);
            $stmt->execute();

            debug(
                "new comment for post $post_id by user $user_id is inserted!",
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

    public function getComments(int $post_id)
    {
            $sql = "SELECT text, first_name, last_name FROM ".$this->tableName." JOIN users ON users.id = comments.user_id WHERE parent_id == :post_id AND is_reply == 0 ORDER BY date_created DESC";
            try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":post_id", $post_id);
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
    }
}
