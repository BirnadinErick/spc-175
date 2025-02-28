<?php

namespace tinyfuse;

use Exception;

class BaseController
{
    protected BaseState $state;

    public function __construct(
        BaseState $state)
    {

        $this->state = $state;
    }

    protected function render(string $view, array $params): string|null
    {
        try {
            ob_start();
            extract($params, EXTR_OVERWRITE);
            require $view . '.php';
            $content = ob_get_clean();
            ob_end_clean();
        } catch (Exception $e) {
            Utils::logInfo($e->getMessage());
            return null;
        }

        return $content;
    }

}