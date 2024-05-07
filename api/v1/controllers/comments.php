<?php

include_once(MODELS . "users.php");
include_once(MODELS . "comments.php");

function comments() {
    debug("comments fetch invoke.", __FILE__);

    if ($_SERVER['REQUEST_METHOD'] === "GET") {
        echo "Getting comments for post" .  $_GET['post_id'] . "...";
    } else {
     session_start();
     $users = new UsersModel();
    $comments = new CommentsModel();

    debug("creating comment in session: " . var_export($_SESSION, true), __FILE__);
    debug("cookies: " . var_export($_COOKIE, true), __FILE__);
     
     $user_id = $users->get_user_id($_SESSION['email']);
     // TODO!: sanitize later
     $post_id = $_POST['post_id'];
     $text = $_POST['text'];

     debug("creating comment for $post_id: \n$text", __FILE__);
     $ok = $comments->addComment($post_id, $text, $user_id);     

     if ($ok) {
         debug("comment created", __FILE__);
         echo "comment added";
         exit();
     } else {
         echo "comment failed";
         exit();
     }
     if ($ok) {
         http_response_code(201);
     } else {
         debug("comment created", __FILE__);
         http_response_code(403);
     }

     header("Location: " . SERVER . "/patrician-publication/" . $post_id);
    session_write_close();
         exit();
    }
}
