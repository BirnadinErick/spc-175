<?php /** @noinspection PhpUndefinedVariableInspection */ ?>
<div id="comments">
    <div class="my-4 border-l-2 pl-2 border-l-gray-500">
        <p class="text-gray-400"><?= ucfirst($fname) . ' ' . strtoupper(substr($lname, 0, 1)) . '.' ?>
            says</p>
        <p style="padding-left: 15px;">
            <?= $comment ?>
        </p>
    </div>
</div>
