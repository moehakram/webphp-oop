<?php
namespace MA\PHPQUICK;

use Exception;
use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Http\Requests\Request;
use MA\PHPQUICK\Router\Route;
use MA\PHPQUICK\Http\Responses\Response;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Router\Runner;
use MA\PHPQUICK\Exception\HttpException;

class Application
{
    public static Application $app;
    public Config $config;
    public Router $router;
    public Response $response;
    public Request $request;

    public function __construct(array $config)
    {
        self::$app = $this;
        $this->config = new Config($config);
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
                $this->setNotFound("Route Not Found { {$this->request->getPath()} }");
            }

            $runner = $this->createRunner($route);

            return $runner->handle($this->request)->send();
        } catch (\MA\PHPQUICK\Exception\HttpException $http) {
            return (new Response($http->getMessage(), $http->getCode(), $http->getHeaders()))->send();
        } catch (\Exception $e) {
            return (new Response($e->getMessage(), $e->getCode()))->send();
        }
    }

    private function createRunner($route): Runner
    {
        $middlewares = array_merge($route->getMiddlewares(), [fn() => $this->handleRouteCallback($route)]);
        return new Runner($middlewares);
    }

    private function handleRouteCallback(Route $route)
    {
        if ($route->getController() === null) {
            return call_user_func_array($route->getAction(), $route->getParameter());
        } else {
            return $this->executeController($route->getController(), $route->getAction(), $route->getParameter());
        }
    }

    private function executeController(string $controller, string $method, $parameter)
    {
        if (class_exists($controller)) {
            $controllerInstance = new $controller();
            if (method_exists($controllerInstance, $method)) {
                return call_user_func_array([$controllerInstance, $method], $parameter);
            } else {
                throw new Exception(sprintf("Method %s not found in %s", $method, $controller), 500);
            }
        } else {
            throw new Exception(sprintf("Controller class %s not found", $controller), 500);
        }
    }

    public function setNotFound($message = null)
    {
        $view = View::render('error/404', [
            'message' => $message
        ]);
        throw new HttpException(404, $view);
    }

}