<button x-on:click="open = !open" x-on:mouseover="open = true" type="button"
        class="inline-flex w-full justify-center gap-x-1.5 rounded-sm px-3 py-2 text-sm font-semibold group-hover:underline group-hover:underline-offset-4"
        id="auth-menu" aria-expanded="true" aria-haspopup="true">
    <span>Login for more!</span>
    <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
         aria-hidden="true">
        <path fill-rule="evenodd"
              d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
              clip-rule="evenodd" />
    </svg>
</button>

<div x-show="open" x-transition:enter="transition ease-out duration-100"
     x-transition:enter-start="transform opacity-0 scale-95"
     x-transition:enter-end="transform opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-75"
     x-transition:leave-start="transform opacity-100 scale-100"
     x-transition:leave-end="transform opacity-0 scale-95"
     class="absolute z-10 mt-2 w-56 origin-top-right space-y-2 rounded-sm bg-spc-bg-mid shadow-lg text-spc-light ring-1 ring-black ring-opacity-5 focus:outline-none"
     role="menu" aria-orientation="vertical" aria-labelledby="menu-button"
     tabindex="-1"
     x-on:mouseleave="open=false"
>
    <div class="py-1" role="none">
        <a href="<?= $login; ?>"
           class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
           role="menuitem" tabindex="-1">Login</a>
        <a href="<?= $sigin; ?>"
           class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
           role="menuitem" tabindex="-1">Register new</a>
    </div>
</div>