<?php

namespace app\controllers;

use app\models\ProjectModel;
use NumberFormatter;
use tinyfuse\BaseController;
use tinyfuse\BaseState;
use tinyfuse\Request;
use tinyfuse\Response;

class ProjectController extends BaseController
{
    private readonly ProjectModel $model;
    private readonly string $views_root;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->model = new ProjectModel($state);
        $this->views_root = $this->state->VIEWS . 'projects/';
    }

    public function request_new_project(Request $request): Response
    {
        $params = $request->get_post_params();
        if (
            !isset($params['title']) && !isset($params['description'])
            && !isset($params['deadline']) && !isset($params['amount'])
        ) {
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