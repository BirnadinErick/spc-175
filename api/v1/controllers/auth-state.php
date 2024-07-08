<?php

require_once MODELS . 'users.php';
function auth_state()
{
    session_start();
    debug("auth_state handler invoke", __FILE__);

    if (isset($_SESSION["email"])) {
        $user = new UsersModel();
        debug("user authenticated", __FILE__);
        header("Cache-Control: max-age=180");
        ?>
        <button x-on:click="open = !open" x-on:mouseover="open = true" type="button"
                class="inline-flex w-full justify-center gap-x-1.5 rounded-sm px-3 py-2 text-sm font-semibold group-hover:underline group-hover:underline-offset-4"
                id="auth-menu" aria-expanded="true" aria-haspopup="true">
            <span><?= $user->get_decorated_name($_SESSION["email"]); ?></span>
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
                <!--general profile actions-->
                <a href="<?= SERVER . '/iam/me' ?>"
                   class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                   role="menuitem" tabindex="-1">Edit Profile</a>
                <a href="<?= SERVER . '/projects/mine' ?>"
                   class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                   role="menuitem" tabindex="-1">My Projects</a>
                <a href="<?= SERVER . '/iam/log' ?>"
                   class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                   role="menuitem" tabindex="-1">My Activity</a>

                <hr />
                <!--special profile actions-->
                <?php if ($user->check_roles_exist(EDITOR_ROLE, $_SESSION["email"])): ?>
                    <a href="<?= SERVER . '/author' ?>"
                       class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                       role="menuitem" tabindex="-1">
                        Manage Contents
                    </a>
                <?php endif; ?>
                <?php if ($user->check_roles_exist(SUPADMIN_ROLE, $_SESSION["email"])): ?>
                    <a href="<?= SERVER . '/iam' ?>"
                       class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                       role="menuitem" tabindex="-1">
                        IAM Dashboard
                    </a>
                <?php endif; ?>

                <!--destructive actions-->
                <form action="<?= API . "logout" ?>" method="post">
                    <button
                        class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                        type="submit"
                    >Logout
                    </button
                    >
                </form>
                <a href="#"
                   class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                   role="menuitem" tabindex="-1">Deactivate Profile</a>
            </div>
        </div>
        <?php
    } else {
        debug("user not authenticated", __FILE__); ?>

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
                <a href="<?= SERVER . '/auth/login'; ?>"
                   class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                   role="menuitem" tabindex="-1">Login</a>
                <a href="<?= SERVER . '/auth/signin'; ?>"
                   class="font-bold block px-4 py-2 text-sm hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                   role="menuitem" tabindex="-1">Register new</a>
            </div>
        </div>
        <?php
    }
    session_write_close();
}
