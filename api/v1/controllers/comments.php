<?php

include_once MODELS . "users.php";
include_once MODELS . "comments.php";

function comments()
{
    debug("comments fetch invoke.", __FILE__);
    $comments = new CommentsModel();

    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        $cs = $comments->getComments(intval($_GET["post_id"]));

        foreach ($cs as $c) { ?>
          <div>
          <div> <b><?= $c['first_name'] . " " . $c['last_name'][0] . "." ?></b> says </div>
                  <p class="text-spc-white/70 pl-2 italic"> <?= $c['text'] ?></p>
          </div>
<?php }
        return;
    } else {
        session_start();
        $users = new UsersModel();

        debug(
            "creating comment in session: " . var_export($_SESSION, true),
            __FILE__
        );
        debug("cookies: " . var_export($_COOKIE, true), __FILE__);

        $user_id = $users->get_user_id($_SESSION["email"]);
        // TODO!: sanitize later
        $post_id = $_POST["post_id"];
        $text = $_POST["text"];

        debug("creating comment for $post_id: \n$text", __FILE__);
        $ok = $comments->addComment(intval($post_id), $text, $user_id);

        header("Location: ". SERVER . "/blogs/$post_id#comments");
        if ($ok) {
            http_response_code(303);
        } else {
            debug("comment failed to commit", __FILE__);
            http_response_code(403);
        }

        session_write_close();
        exit();
    }
}
