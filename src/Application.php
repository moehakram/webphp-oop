<?php

declare(strict_types=1);

namespace MA\PHPQUICK;

use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Router\Route;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Http\Requests\Request;
use MA\PHPQUICK\Http\Responses\Response;
use MA\PHPQUICK\Router\MiddlewarePipeline;
use MA\PHPQUICK\Exception\HttpExceptionInterface;

class Application extends Container
{        
    public function __construct(
        private readonly Request $request,
        private readonly Router $router
    ) {
        self::$instance = $this;
        $this->instance(Request::class, $request);
    }

    public function run()
    {
        try {
            $route = $this->router->dispatch($this->request->getMethod(), $this->request->getPath());
            $middlewarePipeline = $this->createMiddlewarePipeline($route);
            return $middlewarePipeline->handle($this->request)->send();
        } catch (HttpExceptionInterface $httpException) {
            if($HttpExceptionHandler = $this->get('http.exception.handler')){
                $result = ($HttpExceptionHandler)($httpException);
                return $result instanceof Response ? $result->send() : $this->defaultHttpExceptionHandler($httpException)->send();
            }
            return $this->defaultHttpExceptionHandler($httpException)->send();
        }
    }

    private function createMiddlewarePipeline(Route $route): MiddlewarePipeline
    {
        $middlewares = array_merge(
            [AuthMiddleware::class],
            $route->getMiddlewares(),
            [fn() => $this->handleRouteCallback($route)]
        );
        return new MiddlewarePipeline($middlewares, $this->get('middleware.aliases'));
    }

    private function handleRouteCallback(Route $route): mixed
    {
        $action = $route->getAction();
        $arguments = $route->getArguments();
        $arguments[] = $this->request;

        if ($controller = $route->getController()) {
            return $this->get($controller)->$action(...$arguments);
        }

        return call_user_func_array($action, $arguments);
    }

    private function defaultHttpExceptionHandler(HttpExceptionInterface $httpException): Response
    {
        $content = $this->get((string)$httpException->getCode()) ?: $httpException->getMessage();
        $view = $content instanceof View ? $content->with(['message' => $httpException->getMessage()]) : $content;

        return (new Response($view, $httpException->getCode(), $httpException->getHeaders()));
    }
}