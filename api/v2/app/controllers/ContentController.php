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
    private string $contents_root_path;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->model = new ContentModel($state);
        $this->contents_root_path = $this->state->VIEWS . 'contents/';
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
            $this->state->VIEWS . 'editable-contents',
            ["cs" => $contents]
        );

        return new Response($content);
    }

    public function get_content_raw(Request $request): Response
    {
        if ($this->is_anon_user()) {
            return Response::forNotFound();
        }

        $iam_matrix = $this->get_user_roles_matrix(UserRole::from($this->get_user_role()));
        if ($iam_matrix["is_user_editor"] !== true) {
            return Response::forNotAllowed();
        }

        $slug = $request->get_get_param('slug') ?? '';
        $raw_file = $this->model->get_content_raw_file($slug) ?? '';

        $data = $this->render($this->contents_root_path . 'raw/' . $raw_file, [], add_ext: false);
        return new Response($data, type: 'application/json');
    }

    public function get_content_html(Request $request): Response
    {
        $slug = $request->get_get_param('slug') ?? '';
        $html_file = $this->model->get_content_html_file($slug);

        $content = isset($html_file)
            ? $this->render($this->contents_root_path . $html_file, [], false)
            : $this->render($this->contents_root_path . '_404', []);

        return new Response($content);
    }
}