<?php
namespace MA\PHPQUICK;

use MA\PHPQUICK\Router\Route;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Session\Session;
use MA\PHPQUICK\Http\Requests\Request;
use MA\PHPQUICK\Exception\HttpException;
use MA\PHPQUICK\Http\Responses\Response;
use MA\PHPQUICK\Router\MiddlewarePipeline;

class Application
{
    protected static Container $app;
    
    public function __construct(
        protected Container $container,
        protected Request $request,
        protected Router $router
    ){
        $this->bindInstances();
        self::$app = $container;
    }

    protected function bindInstances(): void
    {
        $this->container->instance(Container::class, $this->container);
        $this->container->instance('router', $this->router);
        $this->container->instance('request', $this->request);
        $this->container->instance('response', new Response());
        $this->container->instance('session', new Session);
    }

    public static function __callStatic($name, $arguments): mixed
    {
        return self::$app->get($name);
    }

    public function run()
    {
        try {
            $route = $this->router->dispatch($this->request->getMethod(), $this->request->getPath());
            return $this->createMiddlewarePipeline($route)->handle($this->request)->send();
        } catch (HttpException $http) {
            return (new Response($http->getMessage(), $http->getCode(), $http->getHeaders()))->send();
        }
    }

    private function createMiddlewarePipeline(Route $route): MiddlewarePipeline
    {
        $middlewares = array_merge(
            $this->container->get('middlewareGlobal'),
            $route->getMiddlewares(),
            [fn() => $this->handleRouteCallback($route)]
        );
        return new MiddlewarePipeline($middlewares);
    }

    private function handleRouteCallback(Route $route): mixed
    {
        $action = $route->getAction();
        $arguments = $route->getArguments();
        array_push($arguments, $this->request);

        if ($controller = $route->getController()) {
            return $this->container->get($controller)->$action(...$arguments);
        }

        return call_user_func_array($action, $arguments);
    }
}