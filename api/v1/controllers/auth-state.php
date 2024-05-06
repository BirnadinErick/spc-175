<?php
function auth_state()
{

    session_start();

    debug("auth_state handler invoke", __FILE__);
    debug(var_export($_SESSION, true), __FILE__);

    if (isset($_SESSION['email'])) {

        debug("user authenticated", __FILE__);
        ?>
        <p class="text-sm lg:text-xl font-bold">Herzlich Willkommen Patrician!</p>
        <div class="space-x-2 lg:space-x-0">
            <form action="<?= API . "logout" ?>" method="post">
                <button
                        type="submit"
                        class="bg-spc-dark px-4 text-sm lg:text-lg py-2 hover:bg-spc-dark/80 rounded-sm"
                >Logout
                </button
                >
            </form>
        </div>
        <?php
    } else {

        debug("user not authenticated", __FILE__);
        ?>

        <p class="text-sm lg:text-xl font-bold">Login to enjoy more</p>
        <div class="space-x-2 lg:space-x-0">
            <a
                    href="<?= SERVER . "/auth/login" ?>"
                    class="bg-spc-dark px-4 text-sm lg:text-lg py-2 hover:bg-spc-dark/80 rounded-sm"
            >Login</a
            >
            <a
                    href="<?= SERVER . "/auth/signin" ?>"
                    class="bg-spc-bg-mid px-4 text-sm lg:text-lg py-2 hover:bg-spc-bg-mid/80 rounded-sm"
            >Create New Account</a
            >
        </div>
        <?php
    }
        session_write_close();
}
