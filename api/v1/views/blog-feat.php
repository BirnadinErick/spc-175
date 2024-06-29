<div class="relative">
    <img
        src="<?= $cover ?>"
        alt="<?= $title ?>"
        class="min-h-[256px] h-auto object-cover min-w-full w-full"
        loading="lazy"
        decoding="async"
    />
    <p class="text-2xl font-bold px-6 mt-4 md:px-0">
        <?= $title ?>
        <span class="px-1 text-white/60 text-sm"><?= $date ?></span>
    </p>
</div>

<div class="px-6 my-4 md:p-0">
    <div
        class="bg-gradient-to-br from-spc-sea to-spc-gold p-[3px] max-w-fit text-spc-light"
    >
        <a href="<?= $path ?>">
            <div
                class="bg-spc-dark p-2 md:px-4 md:py-2 flex justify-between items-center space-x-4 md:hover:text-spc-dark md:hover:bg-gradient-to-br md:hover:from-spc-sea md:hover:to-spc-gold md:hover:duration-700 md:hover:ease-out md:hover:animate-color"
            >
                <span class="font-bold font-sans">read feature of the day</span>

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"></path>
                </svg>
            </div>
        </a>
    </div>
</div>
