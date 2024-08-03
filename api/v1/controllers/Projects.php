<?php

namespace tinyfuse\controllers;

require_once MODELS . 'projects.php';

use BumpCore\EditorPhp\Helpers;
use ProjectsModel;

class Projects
{
    public function comment(): void
    {

    }

    /** @noinspection PhpArrayKeyDoesNotMatchArrayShapeInspection */
    public function detail(): void
    {
        //get pid from URL Header and retrieve the content
        $h = getallheaders()['HX-Current-URL'];
        $qs = parse_url($h, PHP_URL_QUERY);
        parse_str($qs, $qs);

        //invoke model to get data
        $p = new ProjectsModel();
        $p = $p->getProject(project_id: $qs['pid']);

        //render the view
        echo Helpers::renderNative(VIEWS.'project-detail.php', ['p'=>$p]);
    }

}