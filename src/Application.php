<?php
namespace MA\PHPQUICK;

use Exception;
use MA\PHPQUICK\Router\Route;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Http\Requests\Request;
use MA\PHPQUICK\Exception\HttpException;
use MA\PHPQUICK\Http\Responses\Response;
use MA\PHPQUICK\Router\MiddlewarePipeline;
use MA\PHPQUICK\Session\Session;

class Application
{
    protected static Application $app;
    protected readonly Router $router;
    protected readonly Config $config;
    protected readonly Response $response;
    protected readonly Request $request;
    protected readonly Session $session;

    public function __construct(array $config)
    {
        self::$app = $this;
        $this->router = new Router();
        $this->config = new Config($config);
        $this->session = new Session;
        $this->request = new Request();
        $this->response = new Response();
    }

    public static function __callStatic($name, $arguments): mixed
    {
        if (property_exists(self::$app, $name)) {
            return self::$app->$name;
        }

        throw new \InvalidArgumentException("Invalid property: $name");
    }

    public function run()
    {
        try {
            $route = $this->router->dispatch($this->request->getMethod(), $this->request->getPath());

            if ($route === null) {
                $this->response->setNotFound("Route Not Found { {$this->request->getPath()} }");
            }

            return $this->createMiddlewarePipeline($route)->handle($this->request)->send();
        } catch (HttpException $http) {
            return (new Response($http->getMessage(), $http->getCode(), $http->getHeaders()))->send();
        }
    }

    private function createMiddlewarePipeline(Route $route): MiddlewarePipeline
    {
        $middlewares = array_merge(
            [AuthMiddleware::class],
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
            return $this->executeController($controller, $action, $arguments);
        }

        return call_user_func_array($action, $arguments);
    }

    private function executeController(string $controller, string $method, array $arguments): mixed
    {
        if (!class_exists($controller)) {
            throw new Exception(sprintf("Controller class %s not found", $controller), 500);
        }

        $controllerInstance = new $controller();

        if (!method_exists($controllerInstance, $method)) {
            throw new Exception(sprintf("Method %s not found in %s", $method, $controller), 500);
        }

        return call_user_func_array([$controllerInstance, $method], $arguments);
    }

}