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

        if (!isset($_SESSION["email"])){
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

            exit();
        }

        debug("failed to create project", __FILE__);
        http_response_code(403);
        session_write_close();
        exit();

    } elseif ($_SERVER["REQUEST_METHOD"] === "GET") {
        $projects = new ProjectsModel();
        $results = $projects->getProjects();

        echo json_encode($results);
        return false;
    } else {
        return false;
    }
}