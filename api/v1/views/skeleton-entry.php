<section class="px-6 mt-8">
    <h1 class="font-bold text-4xl leading-[95%]"><?= $title ?></h1>
    <div
        class="flex justify-start items-baseline font-mono text-[12px] text-spc-light/60 space-x-3 my-3"
    >
        <p>
            <?= $date ?>
        </p>
        <p>/</p>
        <p>SPC Media Unit 2023</p>
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

<section id="comments" class="px-6 my-8 text-spc-light/80 text-sm lg:text-lg">
    <SectionTitle title="Comments..."></SectionTitle>

    <div hx-get="<?= API . 'comments' . '&post_id='. $uid ?>" class="my-4 space-y-4" hx-swap="innerHTML" hx-trigger="load">
    </div>

    <div class="my-6" hx-get="<?= API . 'allowed-to-comment&post_id=' . $uid.'&post_path='.$path ?>" hx-trigger="load delay:300ms">
    </div>
</section>
