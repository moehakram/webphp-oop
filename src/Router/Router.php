<?php

namespace MA\PHPQUICK\Router;
use MA\PHPQUICK\Http\Requests\Request;
use MA\PHPQUICK\Router\Route;

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
        $this->register(Request::GET, $path, $callback, $middlewares);
    }

    public function post(string $path, $callback, ...$middlewares): void
    {
        $this->register(Request::POST, $path, $callback, $middlewares);
    }
    public function put(string $path, $callback, ...$middlewares): void
    {
        $this->register(Request::PUT, $path, $callback, $middlewares);
    }

    public function patch(string $path, $callback, ...$middlewares): void
    {
        $this->register(Request::PATCH, $path, $callback, $middlewares);
    }

    public function delete(string $path, $callback, ...$middlewares): void
    {
        $this->register(Request::DELETE, $path, $callback, $middlewares);
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
        $clean = fn($path) => str_replace(['%20', ' '], '-', rtrim($path, '/')) ?: '/';
        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = '#^' . $clean($route['path']) . '$#';
            if (preg_match($pattern, $clean($path), $variabels)) {
                array_shift($variabels);
                array_push($variabels, $this->request);
                return new Route($route['callback'], $route['middlewares'], $variabels);
            }
        }
        return null;
    }
}
