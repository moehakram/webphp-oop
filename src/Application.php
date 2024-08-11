<?php

declare(strict_types=1);

namespace MA\PHPQUICK;

use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Router\Route;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Http\Requests\Request;
use MA\PHPQUICK\Http\ResponseInterface;
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
            return $this->handleHttpException($httpException)->send();
        }
    }

    private function createMiddlewarePipeline(Route $route): MiddlewarePipeline
    {
        $middlewares = array_merge(
            [AuthMiddleware::class],
            $route->getMiddlewares(),
            [fn() => $this->executeRouteAction($route)]
        );
        return new MiddlewarePipeline($middlewares, $this->get('middleware.aliases'));
    }

    private function executeRouteAction(Route $route): mixed
    {
        $action = $route->getAction();
        $arguments = $route->getArguments();
        $arguments[] = $this->request;

        if ($controller = $route->getController()) {
            return $this->get($controller)->$action(...$arguments);
        }

        return call_user_func_array($action, $arguments);
    }

    private function handleHttpException(HttpExceptionInterface $httpException): Response
    {
        $exceptionHandler = $this->get('http.exception.handler');
        $content = $exceptionHandler ? $exceptionHandler($httpException) : $this->defaultExceptionContent($httpException);
                
        if($content instanceof View){
            $view = $content->with(['message' => $httpException->getMessage()]);
        }elseif($content instanceof ResponseInterface){
            $view = $content->getContent();
        }else{
            $view = $content ?: $this->defaultExceptionContent($httpException);
        }
        return new Response((string)$view, $httpException->getCode(), $httpException->getHeaders());
    }

    private function defaultExceptionContent(HttpExceptionInterface $httpException): mixed
    {
        return $this->get((string)$httpException->getCode()) ?: $httpException->getMessage();
    }
}