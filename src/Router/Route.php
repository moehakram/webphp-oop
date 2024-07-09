<?php

namespace MA\PHPQUICK\Router;

final class Route
{
    private ?string $controller;
    private $action;
    private array $middlewares;
    private array $parameter;

    public function __construct($callback, array $middlewares, $parameter)
    {
        $this->middlewares = $middlewares;
        $this->parameter = $parameter;
        $this->parseCallback($callback);
    }

    private function parseCallback($callback): void
    {
        if (is_array($callback)) {
            [$this->controller, $this->action] = $this->validateControllerAction($callback);
        } elseif(is_string($callback)){
            $handler = explode('@', $callback);
            [$class, $this->action] = $this->validateControllerAction($handler);
            $this->controller = '\\App\\Controllers\\' . $class;
        } elseif(is_callable($callback)) {
            $this->controller = null;
            $this->action = $callback;
        } else {
            throw new \InvalidArgumentException("Invalid callback provided");
        }
    }

    private function validateControllerAction(array $callback): array
    {
        if (count($callback) !== 2) {
            throw new \InvalidArgumentException('Invalid controller action format');
        }

        return $callback;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getParameter(): array
    {
        return $this->parameter;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
