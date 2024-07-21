<?php
namespace MA\PHPQUICK;

use Exception;
use MA\PHPQUICK\Router\Route;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Router\Runner;
use MA\PHPQUICK\Http\Requests\Request;
use MA\PHPQUICK\Exception\HttpException;
use MA\PHPQUICK\Http\Responses\Response;
use MA\PHPQUICK\Session\Session;

class Application
{
    public static Application $app;
    public readonly Config $config;
    public readonly Router $router;
    public readonly Response $response;
    public readonly Request $request;
    public readonly Session $session;

    public function __construct(array $config)
    {
        self::$app = $this;
        $this->config = new Config($config);
        $this->session = new Session;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request);
    }

    public function get(string $path, $callback, ...$middlewares): void
    {
        $this->router->register(Request::GET, $path, $callback, $middlewares);
        
    }

    public function post(string $path, $callback, ...$middlewares): void
    {
        $this->router->register(Request::POST, $path, $callback, $middlewares);
        
    }

    public function run()
    {
        try {
            $route = $this->router->dispatch($this->request->getMethod(), $this->request->getPath());

            if ($route === null) {
                $this->response->setNotFound("Route Not Found { {$this->request->getPath()} }");
            }

            $runner = $this->createRunner($route);

            return $runner->handle($this->request)->send();
        } catch (HttpException $http) {
            return (new Response($http->getMessage(), $http->getCode(), $http->getHeaders()))->send();
        }
    }

    private function createRunner($route): Runner
    {
        $middlewares = array_merge($route->getMiddlewares(), [fn() => $this->handleRouteCallback($route)]);
        return new Runner($middlewares);
    }

    private function handleRouteCallback(Route $route)
    {
        $action = $route->getAction();
        $parameter = $route->getParameter();
        if (($controller = $route->getController()) === null) {
            return call_user_func_array($action, $parameter);
        } else {
            return $this->executeController($controller, $action, $parameter);
        }
    }

    private function executeController(string $controller, string $method, $parameter)
    {
        if (!class_exists($controller)) {
            throw new Exception(sprintf("Controller class %s not found", $controller), 500);
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $method)) {
            throw new Exception(sprintf("Method %s not found in %s", $method, $controller), 500);
        }

        return call_user_func_array([$controllerInstance, $method], $parameter);
    }

}