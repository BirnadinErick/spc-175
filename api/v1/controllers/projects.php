<?php

require_once MODELS . "projects.php";

/**
 * @throws Exception
 *  ^- when the provided date can not be parsed
 */
function projects()
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        debug("new project request", __FILE__);
        session_start();

        if (!isset($_SESSION["email"])) {
            debug("unauthorized request to projects POST handler", __FILE__);
            http_response_code(401);
            exit(1);
        }

        $projects = new ProjectsModel();
        $title = $_POST["project_title"];

        // for different locale
        $amount = $_POST["amount"];
        $desc = $_POST["desc"];
        $deadline = new DateTime($_POST["deadline"]); // TODO: add try-catch for invalid values

        // standardize the data format in data store
        $deadline = $deadline->format("Y-m-d");
        $amount = (float)filter_var($amount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        debug($title . "," . $desc . "," . $amount . "," . $deadline, __FILE__);

        $ok = $projects->addProject($title, $desc, $amount, $deadline);
        if ($ok) {
            debug("project " . $title . " was created", __FILE__);
            http_response_code(201);
            readfile(VIEWS . "new-project-ok.html");
            exit();
        }

        debug("failed to create project", __FILE__);
        http_response_code(403);
        session_write_close();
        exit();

    } elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
        $projects = new ProjectsModel();
        $results = $projects->getProjects();
        $currency_fmt = numfmt_create('de_DE', NumberFormatter::CURRENCY);

        foreach ($results as $r) {
            ?>
            <div class="bg-spc-bg-mid rounded-sm p-4 space-y-2">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold uppercase truncate text-clip">
                        <?= $r['title'] ?>
                    </h2>
                    <div class="flex justify-start items-center space-x-2 text-sm mt-1">
                    <span>
                        <?= $r['status'] ?>
                    </span>
                        <div class="w-[5px] h-[5px] rounded-full bg-white/20"></div>
                        <span class="text-spc-light/70">
                            deadline on <?= $r['deadline']?>
                        </span>
                    </div>
                </div>
                <div>
                    <div class="flex justify-start items-center space-x-3 text-spc-light/90">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                        </svg>

                        <p><?= numfmt_format_currency($currency_fmt, (float) $r['amount'], "EUR") ?> estimated</p>
                    </div>

                    <div class="mt-3 flex justify-start items-center space-x-3 text-spc-light/90">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                        </svg>

                        <p><?= $r['upvote'] ?> Upvotes received</p>
                    </div>
                </div>
            </div>
            <?php
        }
        exit();
    } else {
        return false;
    }
}