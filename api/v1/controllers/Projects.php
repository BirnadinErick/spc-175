<?php /** @noinspection PhpArrayKeyDoesNotMatchArrayShapeInspection */

namespace tinyfuse\controllers;

require_once MODELS . 'projects.php';
require_once MODELS . 'users.php';
require_once MODELS . 'comments.php';

use BumpCore\EditorPhp\Helpers;
use CommentsModel;
use Faker\Extension\Helper;
use JetBrains\PhpStorm\NoReturn;
use NumberFormatter;
use ProjectsModel;
use tinyfuse\lib\Constants;
use tinyfuse\models\UsersModel;

class Projects
{
    private function getProjectIdFromURL(): string|null
    {
        //get pid from URL Header and retrieve the content
        debug(var_export(getallheaders(), true), __FILE__);
        if (getallheaders()['Hx-Current-Url']) {
            $h = getallheaders()['Hx-Current-Url'];
        } else {
            $h = getallheaders()['HX-Current-URL'];
        }
        $qs = parse_url($h, PHP_URL_QUERY);
        parse_str($qs, $qs);

        return $qs['pid'] ?? null;
    }

    #[NoReturn] public function comment(): void
    {
        //get projectid
        $pid = $this->getProjectIdFromURL();
        if (is_null($pid)) {
            http_response_code((int)Constants::BadRequest);
            echo "Bad Request. Contact Support";
            exit(1);
        }
        debug("retrieving comments for $pid", __FILE__);

        //check authd
        session_start();
        $isAuthd = (bool)$_SESSION['email'];
        $p = new ProjectsModel();

        //HTTP Method: GET
        //retrieve comments and render view
        //check for auth-d, render ableToComment
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $cs = $p->getProjectComments($pid);
            echo Helpers::renderNative(VIEWS . 'project-comment.php', [
                'cs' => $cs,
                'isAuth' => $isAuthd
            ]);
//            http_response_code((int)Constants::OK);
            session_write_close();
            exit(0);
        }

        //HTTP Method: POST
        //parse the form and commit the data
        //return rendered view to be appended
        $user = new UsersModel();
        $comments = new CommentsModel();
        $uid = $user->get_user_id($_SESSION['email']);
        $comment = $_POST['comment'];

        //add comment to database
        $cid = $comments->addComment($pid, $comment, $uid);
        if (gettype($cid) == "boolean") {
            debug("failed to record comment for project $pid from user $uid", __FILE__);
            http_response_code((int)Constants::InternalError);
            exit(1);
        }
        //relate project with comment
        $r = $comments->addProjectComment($pid, $cid);
        if ($r === false) {
            debug("failed to relate comment $cid with project $pid", __FILE__);
            http_response_code((int)Constants::InternalError);
            exit(1);
        }

        // http_response_code((int)Constants::Created);
        echo Helpers::renderNative(VIEWS . 'project-new-comment-fragment.php', ['c' => [
            'fname' => $user->get_decorated_name($_SESSION['email']),
            'lname' => '',
            'comment' => $comment
        ]]);
        session_write_close();
        exit(0);
    }

    public function list()
    {
        $projects = new ProjectsModel();
        $results = $projects->getProjects();
        $currency_fmt = numfmt_create('de_DE', NumberFormatter::CURRENCY);

        echo Helpers::renderNative(VIEWS . 'projects-list.php', [
            'results' => $results,
            'currency_fmt' => $currency_fmt
        ]);
        exit(0);
    }

    public function detail(): void
    {
        //get projectid
        $pid = $this->getProjectIdFromURL();
        if (is_null($pid)) {
            http_response_code((int)Constants::BadRequest);
            echo "Bad Request. Contact Support";
            exit(1);
        }

        //invoke model to get data
        $p = new ProjectsModel();
        $p = $p->getProject(project_id: $pid);

        //render the view
        echo Helpers::renderNative(VIEWS . 'project-detail.php', ['p' => $p]);
    }

    function available_projects()
    {
        $c = new ProjectsModel();
        $cs = $c->getProjects();
        echo Helpers::renderNative(VIEWS.'available-projects.php', [
            "cs"=>$cs
        ]);
    }

    public function projects_edit(): void
    {
        session_start();
        if (!isset($_SESSION["email"])) {
            debug("annonymous project edit attempt", __FILE__);
            http_response_code(401);
            exit(1);
        }

        $users = new UsersModel();
        $projects = new ProjectsModel();
        if (!$users->check_roles_exist(PROJADMIN_ROLE, $_SESSION["email"])) {
            debug("unauthorized projects edit attempt", __FILE__);
            http_response_code(401);
            exit(1);
        }

        $headers = getallheaders();
        $url = $headers['HX-Current-URL'];
        $parsed_url = parse_url($url);
        $qstr = $parsed_url['query'] ?? "";
        $q=[];
        parse_str($qstr, $q);
        $id = $q['path'];
        debug("current id:". $id, __FILE__);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            $proj = $projects->getProject($id);
            debug(var_export($proj, true), __FILE__);
            echo Helpers::renderNative(VIEWS . 'edit-project.php' , ['proj'=>$proj]);
            exit(0);
        }

        $formData = [
            'project_title' => $_POST['project_title'] ?? null,
            'desc'          => $_POST['desc'] ?? null,
            'deadline'      => $_POST['deadline'] ?? null,
            'amount'        => $_POST['amount'] ?? null,
            'status'        => $_POST['status'] ?? null,
            'id'=> $id,
        ];
        debug(var_export($formData, true), __FILE__);
        $res = $projects->editProject($formData);
        if (!$res) {
            debug("project edit failed", __FILE__);
            echo 'Something went wrong, please hard refresh and try again';
            exit(1);
        }

        http_response_code(307);
        echo 'Saved changes.';
        exit(0);

    }
}