<?php /** @noinspection PhpUndefinedVariableInspection */ ?>
<div id="comments">
        <?php foreach ($comments as $comment): ?>
            <div class="my-4 border-l-2 pl-2 border-l-gray-500">
                <p class="text-gray-400"><?= ucfirst($comment['fname']) . ' ' . strtoupper(substr($comment['lname'], 0, 1)) ?>
                    says</p>
                <p style="padding-left: 15px;">
                    <?= $comment['comment'] ?>
                </p>
            </div>
        <?php endforeach;?>

</div>

<?php if ($is_user_authed === true) : ?>
    <div class="space-y-2 py-4 htmxenable">
    <textarea id="comment" class="bg-spc-bg-mid text-spc-light w-full" rows="5" name="comment" autofocus
              spellcheck="true" placeholder="Let us know what you think about this project"></textarea>
        <button hx-include="#comment" hx-post="<?= $API . 'new-project-comment' ?>"
                hx-swap="none" hx-select-oob="#comments:beforeend"
                class="px-4 font-bold py-1 bg-spc-bg-mid block rounded-sm htmxenable">Comment.
        </button>
    </div>
<?php else: ?>
    <p class="text-spc-light/50">
        Please <a class="text-blue-400 underline underline-offset-4" href="/auth/login">Log in</a>
        to add your comment.</p>
<?php endif; ?>
