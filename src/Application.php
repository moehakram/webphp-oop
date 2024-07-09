<?php
namespace MA\PHPQUICK;

use App\Domain\User;
use MA\PHPQUICK\Http\Request;
use MA\PHPQUICK\Router\Route;
use MA\PHPQUICK\Http\Response;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Router\Runner;

class Application
{
    public static Application $app;
    public Router $router;
    public array $config;
    public ?User $user = null;

    public Response $response;
    public Request $request;

    public function __construct(array $config)
    {
        self::$app = $this;
        $this->config = $config;
        $this->request = new Request;
        $this->response = new Response;
        $this->router = new Router($this->request);
    }

    public function get(string $path, $callback, ...$middlewares): void
    {
        $this->router->register('GET', $path, $callback, $middlewares);
        
    }

    public function post(string $path, $callback, ...$middlewares): void
    {
        $this->router->register('POST', $path, $callback, $middlewares);
        
    }

    public function run()
    {
        try {
            $route = $this->router->dispatch($this->getMethod(), $this->getPath());

            if ($route === null) {
                $this->response->setNotFound('Route Not Found');
            }

            $runner = $this->createRunner($route);

            return $runner->handle($this->request);
        } catch (\Throwable $th) {
            return $this->response->setContent($th->getMessage())->setStatusCode($th->getCode());
        } finally{
            $this->response->send();
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
            $content = call_user_func_array($route->getAction(), $route->getParameter());
            $this->response->setContent($content);
        } else {
            $this->executeController($route->getController(), $route->getAction(), $route->getParameter());
        }
    }

    private function executeController(string $controller, string $method, $parameter)
    {
        if (class_exists($controller)) {
            $controllerInstance = new $controller();
            if (method_exists($controllerInstance, $method)) {
                $content = call_user_func_array([$controllerInstance, $method], $parameter);
                $this->response->setContent($content);
            } else {
                $this->response->setNotFound(sprintf("Method %s not found in %s", $method, $controller));
            }
        } else {
            $this->response->setNotFound(sprintf("Controller class %s not found", $controller));
        }
    }

    private function cleanPath($path): string
    {
        return ($path === '/') ? $path : str_replace(['%20', ' '], '-', rtrim($path, '/'));
    }

    private function getPath(): string
    {
        return $this->cleanPath($this->request->getPath());
    }

    private function getMethod(): string
    {
        return strtoupper($this->request->getMethod());
    }

}