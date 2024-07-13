<?php

namespace MA\PHPQUICK\Router;
use MA\PHPQUICK\Http\Request;
use MA\PHPQUICK\Router\Route;
use MA\PHPQUICK\Http\Requests\Request as Req;

class Router
{
    private array $routes = [];
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get(string $path, $callback, ...$middlewares): void
    {
        $this->register(Req::GET, $path, $callback, $middlewares);
    }

    public function post(string $path, $callback, ...$middlewares): void
    {
        $this->register(Req::POST, $path, $callback, $middlewares);
    }
    public function put(string $path, $callback, ...$middlewares): void
    {
        $this->register(Req::PUT, $path, $callback, $middlewares);
    }

    public function patch(string $path, $callback, ...$middlewares): void
    {
        $this->register(Req::PATCH, $path, $callback, $middlewares);
    }

    public function delete(string $path, $callback, ...$middlewares): void
    {
        $this->register(Req::DELETE, $path, $callback, $middlewares);
    }

    public function register(string $method, string $path, $callback, array $middlewares): void
    {
        $this->routes[$method][] = [
            'path' => $path,
            'callback' => $callback,
            'middlewares' => $middlewares
        ];
    }

    public function dispatch(string $method, string $path): ?Route
    {
        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = '#^' . $route['path'] . '$#';
            if (preg_match($pattern, $path, $variabels)) {
                array_shift($variabels);
                $variabels[] = $this->request;
                return new Route($route['callback'], $route['middlewares'], $variabels);
            }
        }
        return null;
    }

}
