## Table of Contents

- [Table of Contents](#table-of-contents)
- [About The Project](#about-the-project)
- [Getting Started](#getting-started)
- [Usage](#usage)
  - [Configuration](#configuration)
  - [Routing Patterns](#routing-patterns)
  - [Callback Formats](#callback-formats)
    - [Summary](#summary)
  - [Implementing Middleware](#implementing-middleware)

## About The Project

This framework is a simple solution for native PHP projects that integrates object-oriented programming (OOP) principles. It is designed to facilitate PHP application development by providing tools for streamlined routing management.

## Getting Started

To start using this framework, follow these steps:

1. Clone this repository to your local directory.
2. Run `composer install` to install the necessary dependencies.

## Usage

### Configuration
Database configuration is located in `/config/config.php`.

To start a local server, use the following command:

```bash
composer serve
```

### Routing Patterns
Routing configuration is located in `/config/routes.php`.

```php
// Example: `http://localhost:8080/users/123`
$router->get('/users/:id', function($id, Request $req) {
    return "User ID: " . $id;  // Output: User ID: 123
});

// Example: `http://localhost:8080/users/asd123`
$router->get('/users/:id', function($id, Request $req) {
    return "User ID: " . $id;  // Output: User ID: asd123
});

// Example: `http://localhost:8080/search/name_group/123`
$router->get('/search/:group/:id', function($group, $id, Request $req) {
    return "Group / ID: " . $group . '/' . $id;  // Output: Group / ID: name_group/123
});
```

### Callback Formats

The framework accepts various callback formats:

1. **Array Callback:**

    The callback should be an array with two elements: the controller class name and the method name.

    ```php
    $router->get('/array', [HomeController::class, 'index']);
    ```

2. **String Callback:**

    The callback should be a string in the format `'Controller@method'`, where `Controller` is the controller class name and `method` is the method name.

    ```php
    $router->get('/string', 'HomeController@index');
    ```

3. **Callable Callback:**

    The callback should be a callable, such as a closure, anonymous function, or any function defined as callable.

    ```php
    $router->get('/anonymous-function', function() {
        return "Hello World";
    });
    
    $router->get('/arrow-function', fn() => "Hello World");
    ```

#### Summary

- **Array:** `[ControllerClass::class, 'methodName']`
- **String:** `'Controller@method'`
- **Callable:** `function(Request $req) { ... }` or `fn(Request $req) => ...`

### Implementing Middleware

You can add middleware to your routes by passing them as additional parameters to the `get` method.

```php
// Middleware example
class AuthMiddleware implements Middleware {
    public function execute(Request $request, callable $next)
    {
        // Authentication logic
        if (!$request->user) {
            return redirect('/login');
        }
        
        return $next($request);
    }
}

// Applying middleware to a route
//$router->get(string $path, $callback, ...$middlewares): void
$router->get('/dashboard', 'DashboardController@index', AuthMiddleware::class);
```