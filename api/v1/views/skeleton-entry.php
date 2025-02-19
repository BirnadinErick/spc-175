<section class="px-6 mt-8">
    <h1 class="font-bold text-4xl leading-[95%]"><?= $title ?></h1>
    <div
        class="flex justify-start items-baseline font-mono text-[12px] text-spc-light/60 space-x-3 my-3"
    >
        <p>
            <?= $date ?>
        </p>
        <p>/</p>
        <p>SPC Media Unit</p>
    </div>
</section>

<div class="mt-6">
    <img
        src="<?= $cover ?>"
        alt="<?= $title ?>"
        class="w-full object-cover max-h-[512px]"
    />
</div>

<article
    class="mb-20 mt-8 prose-sm prose-headings:px-6 prose-headings:font-bold prose-h1:text-3xl prose-h2:prose-xl prose-ul:list-disc prose-ol:list-decimal prose-ul:px-16 prose-p:px-6 xl:prose-p:px-24 md:prose-p:text-lg text-spc-light/75"
>
    <?= $blog ?>
</article>

<section class="px-6">
    <?php foreach ($tags as $t): ?>
        <span class="first:pr-1 last:pl-1 px-1 text-spc-light/50 font-mono text-sm">
            #<?= $t ?>
        </span>
    <?php endforeach; ?>
</section>

<section class="px-6 my-12 ">
    <h3 class="text-spc-light font-bold text-xl">Comments so far.</h3>

    <div class="htmxenable" hx-get="<?= API . 'get-blog-comments' . '&slug=' . $slug ?>"
         hx-swap="outerHTML" hx-trigger="load delay:200ms">
        Loading comments, please wait...
    </div>
</section>