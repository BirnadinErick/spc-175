<?php

function allowed_to_comment()
{
    session_start();
    debug("checking whether user allowed to comment", __FILE__);
    debug("recieved arrays are: " . var_export(getallheaders(), true), __FILE__);

    $post_id = $_GET['post_id'];

    if (!isset($_SESSION['email'])) { ?>
        <p class="text-spc-light/50">
            Please <a class="text-blue-400 underline underline-offset-4" href="/auth/login">Log in</a>
            to add your comment.</p>
        <?php
    } else { ?>
        <form class="space-y-2" method="post" action="<?= API . "comments&post_id=" . $post_id ?>">
            <label class="text-spc-light/60" for="text">Add your comment:</label>
            <textarea class="block border-0 bg-spc-bg-mid text-white text-sm p-1 lg:text-lg w-full" id="text"
                      name="text"
                      placeholder="Let the world know what you think!" rows="5"></textarea>
            <input type="hidden" name="post_id" value="<?= $post_id ?>">
            <button class="flex justify-around items-center space-x-2 bg-spc-bg-mid text-spc-light text-sm px-4 py-1 lg:text-lg rounded-sm"
                    type="submit">
                <span>Comment</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/>
                </svg>
            </button>
        </form>
        <?php
    }

    session_write_close();
    exit();
}