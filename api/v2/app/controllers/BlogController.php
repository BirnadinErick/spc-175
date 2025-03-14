<?php

namespace app\controllers;

use app\models\BlogModel;
use app\models\ContentModel;
use tinyfuse\AuthUtils;
use tinyfuse\BaseController;
use tinyfuse\BaseState;
use tinyfuse\IAMUtils;
use tinyfuse\renderer\ContentRenderer;
use tinyfuse\Request;
use tinyfuse\Response;
use tinyfuse\STATUS_CODES;
use tinyfuse\UserRole;
use tinyfuse\Utils;

class BlogController extends BaseController
{
    use AuthUtils, IAMUtils, ContentRenderer;

    private readonly BlogModel $model;
    private string $blogs_root_path;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->model = new BlogModel($state);
        $this->blogs_root_path = $this->state->VIEWS . 'blogs/';
    }

    public function editable_blogs(Request $request): Response
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
            $this->state->VIEWS . 'editable-blogs',
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

        $data = $this->render($this->blogs_root_path . 'raw/' . $raw_file, [], add_ext: false);
        return new Response($data, type: 'application/json');
    }

    public function update_content(Request $request): Response
    {
        if ($this->is_anon_user()) {
            return Response::forNotFound();
        }

        $iam_matrix = $this->get_user_roles_matrix(UserRole::from($this->get_user_role()));
        if ($iam_matrix["is_user_editor"] !== true) {
            return Response::forNotAllowed();
        }
        $editor_id = $this->get_user_id($this->state);

        $params = $request->get_post_params();
        if (!isset($params['data']) && !isset($params['slug'])) {
            return Response::forNotFound();
        }


        return $this->model->update_content(
            $params['slug'],
            $editor_id,
            $params['data'],
            $this->content_render($params['data'])
        )
            ? new Response('Saved', code: STATUS_CODES::UPDATED)
            : Response::forFailedAction();
    }

    public function get_content_html(Request $request): Response
    {
        $slug = $request->get_get_param('slug') ?? '';
        $html_file = $this->model->get_content_html_file($slug);

        $content = isset($html_file)
            ? $this->render($this->blogs_root_path . $html_file, [], false)
            : $this->render($this->blogs_root_path . '_404', []);

        return new Response($content);
    }
}