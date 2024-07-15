<?php

namespace MA\PHPQUICK\Router;

use MA\PHPQUICK\Http\Requests\Request;
use MA\PHPQUICK\Interfaces\Middleware as MiddlewareInterface;

class Runner
{
    private int $index = 0;
    private array $middlewares = [];

    public function __construct(array $middlewares)
    {
        array_walk($middlewares, [$this, 'addMiddleware']);
    }

    private function addMiddleware($middleware): void
    {
        if(is_string($middleware) && class_exists($middleware)){
            $middleware = new $middleware;
        }

        $this->middlewares[] = $middleware;
    }

    public function handle(Request $request)
    {
        $middleware = $this->middlewares[$this->index];
        if (!isset($this->middlewares[$this->index])) {
            return response();
        }

        $result = $this->executeMiddleware($middleware, $request);
        return $this->createResponse($result);
    }

    private function createResponse($result){
        if (is_scalar($result)) {
            return response($result);
        }else{
            return $result;
        }
    }

    public function __invoke(Request $request)
    {
        return $this->handle($request);
    }

    private function executeMiddleware($middleware, Request $request)
    {
        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->execute($request, $this->next());
        } elseif (is_callable($middleware)) {
            return $middleware();
        }
    }

    private function next()
    {
        $this->index++;
        return $this;
    }
}

