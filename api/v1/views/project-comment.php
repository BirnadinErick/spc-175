<?php if (gettype($cs) == "integer"): ?>
<p class="my-4">
    No comments yet. Be the first to add your opinion
</p>
<?php else:
    foreach ($cs as $c): ?>
        <div class="my-4 space-y-4">
            <div>
                <p class="text-white/20"><?= $c['fname'] . ' ' . $c['lname'] ?> says</p>
                <p class="text-white my-1">
                    <?= $c['comment'] ?>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($isAuth === true) : ?>
    <div class="space-y-2 py-4">
    <textarea id="comment" class="bg-spc-bg-mid text-spc-light w-full" rows="5" name="comment" autofocus
              spellcheck="true"></textarea>
        <button hx-include="#comment" hx-post="<?= API . 'project-comment' ?>"
                class="px-4 font-bold py-1 bg-spc-bg-mid block rounded-sm">Comment.
        </button>
    </div>
<?php else: ?>
    <p class="text-spc-light/50">
        Please <a class="text-blue-400 underline underline-offset-4" href="/auth/login">Log in</a>
        to add your comment.</p>
<?php endif; ?>