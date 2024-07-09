<?php

namespace MA\PHPQUICK\MVC;

abstract class Controller
{
    protected $layout = '';

    protected function view(string $view, array $data = [])
    {
        return View::render($view, $data, $this->layout);
    }

    protected function model(string $modelName)
    {
        $modelClass = "\\App\\Models\\" . $modelName;

        $this->checkModelClass($modelClass);

        return new $modelClass;
    }

    private function checkModelClass(string $modelClass)
    {
        if (!class_exists($modelClass)) {
            throw new \Exception(sprintf('{ %s } this model class not found', $modelClass));
        }
    }
}
