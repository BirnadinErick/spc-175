<article
    class="my-8 hover:bg-[#393939]/40 py-4 md:px-4 transition-colors duration-300 ease-out"
>
    <a href="">
        <img
            src=
            alt=
            class="min-h-[256px] h-auto  object-cover min-w-full w-full"
        loading="lazy"
        decoding="async"
        />
        <div class="flex justify-between items-baseline my-3">
            <h3 class="font-bold text-lg">{title}</h3>
            <p class="font-mono text-[10px]">{date}</p>
        </div>

        <p class="my-3 text-sm text-spc-light/80">
            <?= $desc ?>
        </p>
    </a>

    <div class="w-[25px] h-[5px] mt-6 bg-[#393939]/70 rounded-full mx-auto"></div>
</article>
