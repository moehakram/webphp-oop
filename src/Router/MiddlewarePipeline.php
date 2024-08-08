<?php

namespace MA\PHPQUICK\Router;

use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Interfaces\Middleware;
use MA\PHPQUICK\Interfaces\Response;
use MA\PHPQUICK\MVC\View;

class MiddlewarePipeline
{
    private int $index = 0;
    private array $middlewares = [];

    public function __construct(array $middlewares)
    {
        $this->middlewares = array_map([$this, 'resolveMiddleware'], $middlewares);
    }

    private function resolveMiddleware($middleware)
    {
        if (is_string($middleware) && class_exists($middleware)) {
            return new $middleware;
        }

        if (is_callable($middleware) || $middleware instanceof Middleware) {
            return $middleware;
        }

        throw new \InvalidArgumentException('Invalid middleware provided.');
    }

    public function handle(Request $request): Response
    {
        if (!isset($this->middlewares[$this->index])) {
            return response();
        }

        $middleware = $this->middlewares[$this->index];

        $result = $this->executeMiddleware($middleware, $request);

        return $this->createResponse($result);
    }

    private function executeMiddleware($middleware, Request $request)
    {
        if ($middleware instanceof Middleware) {
            return $middleware->execute($request, $this->next());
        }

        return $middleware($request, $this->next());
    }

    private function createResponse($result): Response
    {
        if ($result instanceof Response) {
            return $result;
        }
    
        if ($result instanceof View) {
            return $this->createViewResponse($result);
        }
    
        return response($result);
    }
    
    private function createViewResponse(View $view): Response
    {
        return response($view->display());
    }    

    private function next(): \Closure
    {
        return function (Request $request) {
            $this->index++;
            return $this->handle($request);
        };
    }
}
