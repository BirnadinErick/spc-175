<?php /** @noinspection ALL */

require_once MODELS . "base.php";

class CommentsModel extends BaseModel
{
    protected string $tableName = "comments";

    public function addComment(string $post_id, string $text, int $user_id): bool
    {
        $sql =
            "INSERT INTO " .
            $this->tableName .
            " (text, user_id, parent_id, is_reply, upvotes, date_created) 
        VALUES (:text, :user_id, :parent_id, :is_reply, :upvotes, :date_created)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(":text", $text);
            $stmt->bindValue(":user_id", $user_id);
            $stmt->bindValue(":parent_id", $post_id);
            // since this is a comment, features of a reply is set to NULL
            $stmt->bindValue(":is_reply", 0);
            $stmt->bindValue(":upvotes", 0);
            $stmt->bindValue(":date_created", date('Y-m-d'));
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

    public function getComments(string $post_id)
    {
            $sql = "SELECT text, first_name, last_name FROM comments  JOIN users ON users.id = comments.user_id WHERE parent_id = :post_id AND is_reply = 0 ORDER BY date_created DESC";
            debug($sql, __FILE__);

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
