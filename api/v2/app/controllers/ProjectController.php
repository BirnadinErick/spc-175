<?php

namespace app\controllers;

use app\models\ProjectModel;
use NumberFormatter;
use tinyfuse\AuthUtils;
use tinyfuse\BaseController;
use tinyfuse\BaseState;
use tinyfuse\IAMUtils;
use tinyfuse\Request;
use tinyfuse\Response;
use tinyfuse\UserRole;

class ProjectController extends BaseController
{
    use AuthUtils, IAMUtils;

    private readonly ProjectModel $model;
    private readonly string $views_root;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->model = new ProjectModel($state);
        $this->views_root = $this->state->VIEWS . 'projects/';
    }

    public function get_project_detail(Request $request): Response
    {
        $project_id = $this->retrieve_project_id() ?? -2003;

        $project = $this->model->get_project($project_id);
        if (!isset($project['title'])) {
            return Response::forNotFound();
        }

        $content = $this->render($this->views_root . 'project-detail', [
            'project' => $project
        ]);
        return new Response($content);
    }

    private function retrieve_project_id(): int|null
    {
        //get pid from URL Header and retrieve the content
        $h = getallheaders()['HX-Current-URL'];
        $qs = parse_url($h, PHP_URL_QUERY);
        parse_str($qs, $qs);

        /** @noinspection PhpArrayKeyDoesNotMatchArrayShapeInspection */
        return intval($qs['pid']) ?? null;
    }

    public function admin_project_list(Request $_): Response
    {
        if ($this->is_user_not_allowed()) {
            return Response::forNotFound();
        }

        $projects = $this->model->get_all_project();
        $content = $this->render(
            $this->views_root . 'admin-projects-list', [
                "projects" => $projects
            ]
        );
        return new Response($content);
    }

    private function is_user_not_allowed(): bool
    {
        if ($this->is_anon_user()) {
            return true;
        }

        $iam_matrix = $this->get_user_roles_matrix(UserRole::from($this->get_user_role()));
        if ($iam_matrix['is_user_padmin'] !== true) {
            return true;
        }

        return false;
    }

    public function update_project(Request $request): Response
    {
        if ($this->is_user_not_allowed()) {
            return Response::forNotAllowed();
        }

        $params = $request->get_post_params();
        if (!$this->does_params_have_needed_properties($params)) {
            return Response::forNotAllowed();
        }

        $project_id = $this->retrieve_project_id() ?? -2003;
        $ok = $this->model->update_project($project_id, $params);

        return $ok
            ? new Response('Changes saved. Go back to the dashboard')
            : Response::forFailedAction();
    }

    private function does_params_have_needed_properties(array $params): bool
    {
        if (
            !isset($params['title']) && !isset($params['description'])
            && !isset($params['deadline']) && !isset($params['amount'])
        ) {
            return false;
        }

        return true;
    }

    public function project_edit_form(Request $request): Response
    {
        if ($this->is_user_not_allowed()) {
            return Response::forNotAllowed();
        }

        $project_id = $this->retrieve_project_id() ?? -2003;
        if ($this->is_user_not_allowed() || $project_id === -2003) {
            return Response::forNotFound();
        }

        $project = $this->model->get_project($project_id);
        if (!isset($project['title'])) {
            return Response::forNotFound();
        }

        $content = $this->render(
            $this->views_root . 'edit-project-form',
            ["project" => $project]
        );
        return new Response($content);
    }

    public function request_new_project(Request $request): Response
    {
        $params = $request->get_post_params();
        if (!$this->does_params_have_needed_properties($params)) {
            return Response::forNotAllowed();
        }

        return $this->model->new_project($params)
            ? new Response('Created, please wait for approval from admins')
            : Response::forFailedAction();
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function get_project_list(Request $_): Response
    {
        $currency_fmt = numfmt_create('de_DE', NumberFormatter::CURRENCY);
        $projects = $this->model->get_all_project();
        $content = $this->render($this->views_root . 'projects-list', [
            'projects' => $projects,
            'API' => $this->state->get_env('API'),
            'currency_fmt' => $currency_fmt
        ]);
        return new Response($content);
    }
}