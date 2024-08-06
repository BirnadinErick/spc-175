<h1 class="text-spc-sea text-3xl font-serif"><?= $p['title'] ?></h1>
<p class="text-spc-light/80 my-4"><?= $p['description'] ?></p>

<div class="text-spc-light/80 my-4 space-y-2">
    <p>Estimated to cost <?= $p['amount'] ?> Euros.</p>
    <p>Status: <?= $p['status'] ?></p>
    <p>To be completed on <?= $p['deadline'] ?></p>
</div>
