<?php ?>

<div class="relative cursor-pointer flex-none w-11/12 md:w-1/3 mr-4 md:pb-4">
    <a href="<?= $href ?>">
        <img
            src="<?= $cover ?>"
            class="rounded-md"
            alt="<?= $title ?>"
            loading="lazy"
            decoding="async"
        />
        <div
            class="font-bold absolute bottom-0 rounded-b-md backdrop-blur-[5px] bg-spc-dark/40 w-full p-2"
        >
            <h3 class="text-xl"><?= $title ?></h3>
            <p class="text-sm text-spc-light/70 font-mono"><?= $date ?></p>
        </div>
    </a>
</div>
