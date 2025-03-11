<?php

namespace app\controllers;

use app\models\ContentModel;
use tinyfuse\BaseController;
use tinyfuse\BaseState;
use tinyfuse\Request;
use tinyfuse\Response;

class ContentController extends BaseController
{
    private readonly ContentModel $model;

    public function __construct(BaseState $state)
    {
        parent::__construct($state);
        $this->model = new ContentModel($state);
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