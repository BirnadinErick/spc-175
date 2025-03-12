<?php

namespace app\controllers;

use app\models\ContentModel;
use tinyfuse\AuthUtils;
use tinyfuse\BaseController;
use tinyfuse\BaseState;
use tinyfuse\IAMUtils;
use tinyfuse\Request;
use tinyfuse\Response;
use tinyfuse\UserRole;

class ContentController extends BaseController
{
    use AuthUtils, IAMUtils;

    private readonly ContentModel $model;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->model = new ContentModel($state);
    }

    public function editable_contents(Request $request): Response
    {
        if ($this->is_anon_user()) {
            return Response::forNotFound();
        }

        $iam_matrix = $this->get_user_roles_matrix(UserRole::from($this->get_user_role()));
        if ($iam_matrix["is_user_editor"] !== true) {
            return Response::forNotAllowed();
        }

        $contents = $this->model->get_editable_meta();
        $content = $this->render(
            $this->state->VIEWS.'editable-contents',
            ["cs"=>$contents]
        );

        return new Response($content);
    }

    public function get_content_html(Request $request): Response
    {
        $contents_root_path = $this->state->VIEWS . 'contents/';

        $slug = $request->get_get_param('slug') ?? '';
        $html_file = $this->model->get_content_html_file($slug);

        $content = isset($html_file)
            ? $this->render($contents_root_path . $html_file, [], false)
            : $this->render($contents_root_path . '_404', []);

        return new Response($content);
    }
}