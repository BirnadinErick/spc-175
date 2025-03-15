<?php /** @noinspection PhpUndefinedVariableInspection */ ?>
<div
    x-data="{ expanded: false }"
>
    <button
        class="text-lg font-bold mb-2 flex justify-between space-x-8 w-full items-center"
        @click="expanded = ! expanded"
    >
        <span>Account Options</span>
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewbox="0 0 24 24"
            stroke-width="3.5"
            stroke="currentcolor"
            class="size-4 transform transition-transform duration-500 ease-out"
            :class="expanded ? 'rotate-90' : 'rotate-0'">
            >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="m8.25 4.5 7.5 7.5-7.5 7.5"
            />
        </svg>
    </button>
    <ul x-show="expanded" x-collapse class="pl-2 space-y-2">
        <!--        <li>-->
        <!--            <a href="/iam/me"-->
        <!--               x-on:click="open = false"-->
        <!--               class=" block px-2 py-2  hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out">-->
        <!--                Edit Profile-->
        <!--            </a>-->
        <!--        </li>-->
        <!--        <li>-->
        <!--            <a href="/projects/mine"-->
        <!--               x-on:click="open = false"-->
        <!--               class=" block px-2 py-2  hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out">-->
        <!--                My Projects-->
        <!--            </a>-->
        <!--        </li>-->
        <!--        <li>-->
        <!--            <a href="/iam/log"-->
        <!--               x-on:click="open = false"-->
        <!--               class=" block px-2 py-2  hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out">-->
        <!--                My Activity-->
        <!--            </a>-->
        <!--        </li>-->
        <li>
            <form class="" action="<?= $API . "logout-user" ?>" method="post">
                <button
                    class=" px-2 py-2  hover:bg-spc-gold hover:text-black transition-colors duration-200 ease-in-out"
                    type="submit">
                    Logout
                </button>
            </form>
        </li>
    </ul>
</div>
