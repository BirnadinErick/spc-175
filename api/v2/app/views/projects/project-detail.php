<?php /** @noinspection PhpUndefinedVariableInspection */ ?>
<h1 class="text-spc-sea text-3xl font-serif"><?= $project['title'] ?></h1>
<p class="text-spc-light/80 my-4"><?= $project['description'] ?></p>

<div class="text-spc-light/80 my-4 space-y-2">
    <p>Estimated to cost <?= $project['amount'] ?> Euros.</p>
    <p>Status: <?= $project['status'] ?></p>
    <p>To be completed on <?= $project['deadline'] ?></p>
</div>
