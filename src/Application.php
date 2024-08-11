<?php

declare(strict_types=1);

namespace MA\PHPQUICK;

use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Router\Route;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Http\RequestInterface;
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
        $this->instance(RequestInterface::class, $request);
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
        $globalMiddlewares = $this->has('middleware.global') ? $this->get('middleware.global') : [];
        $routeMiddlewares = $route->getMiddlewares();

        $middlewares = array_merge(
            $globalMiddlewares,
            $routeMiddlewares,
            [fn() => $this->executeRouteAction($route)]
        );
        return new MiddlewarePipeline($middlewares, $this->get('middleware.aliases'));
    }

    private function executeRouteAction(Route $route): mixed
    {
        $action = $route->getAction();
        $arguments = $route->getArguments();

        if ($controller = $route->getController()) {
            $controllerInstance = $this->get($controller);

            if (!method_exists($controllerInstance, $action)) {
                throw new \BadMethodCallException("Method {$action} not found in controller {$controller}");
            }

            return $this->call([$controllerInstance, $action], $arguments); // Menggunakan metode call dari container
        }

        return $this->call($action, $arguments); // Menggunakan metode call dari container untuk fungsi callback biasa
    }

    private function handleHttpException(HttpExceptionInterface $httpException): Response
    {
        $handler = $this->has('http.exception.handler') 
        ? $this->get('http.exception.handler') 
        : null;

        $content = $handler ? $handler($httpException) : $this->defaultExceptionContent($httpException);

        // Determine the content type and prepare the response
        if ($content instanceof View) {
            $view = $content->with(['message' => $httpException->getMessage()]);
        } elseif ($content instanceof ResponseInterface) {
            $view = $content->getContent();
        } else {
            $view = $content ?: $this->defaultExceptionContent($httpException);
        }
        return new Response((string)$view, $httpException->getCode(), $httpException->getHeaders());
    }

    private function defaultExceptionContent(HttpExceptionInterface $httpException): mixed
    {
        return $this->get((string)$httpException->getCode()) ?: $httpException->getMessage();
    }
}