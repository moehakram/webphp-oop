<!-- TABLE OF CONTENTS -->
## Table of Contents

- [Table of Contents](#table-of-contents)
- [About The Project](#about-the-project)
- [Getting Started](#getting-started)
- [Usage](#usage)
  - [Configuration (`config.php`)](#configuration-configphp)
  - [Entry Point (`index.php`)](#entry-point-indexphp)
  - [Routing Patterns](#routing-patterns)
  - [Callback Formats](#callback-formats)
    - [Summary](#summary)
  - [Implementing Middleware](#implementing-middleware)

<!-- ABOUT THE PROJECT -->
## About The Project
<!-- Add a brief description about the project here -->
This framework is a simple framework for native PHP projects that integrates object-oriented programming (OOP) principles. It is designed to facilitate PHP application development by providing tools for simple routing management.

<!-- GETTING STARTED -->
## Getting Started
<!-- Add instructions on how to get started with the project here -->
To start using this framework, follow these steps:

1. Clone this repository to your local directory.
2. Run `composer install` to install the necessary dependencies.
3. Configure the `index.php` file as needed.

<!-- USAGE EXAMPLES -->
## Usage
### Configuration (`config.php`)
```php
return [
    'dir' => [
        'views' => __DIR__ . '/app/views/'
    ],
    'database' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'php_mvc',
        'username' => 'root',
        'password' => ''
    ]   
];
```

### Entry Point (`index.php`)
```php
// Include the autoloader to load necessary classes
require_once __DIR__ . '/vendor/autoload.php';

// Load the configuration
$config = require __DIR__ . '/config.php';

$app = PHPQuick($config);

$app->get('/', 'HomeController@index');

$app->run();
```

To start a local server, use the following command:
```bash
php -S localhost:8080
```

### Routing Patterns
```php
// `http://localhost:8080/users/123`
$app->get('/users/:id', function($id, Request $req) {
    return "User ID: " . $id;  // User ID: 123
});

// `http://localhost:8080/users/asd123`
$app->get('/users/:id', function($id, Request $req) {
    return "User ID: " . $id; // User ID: asd123
});

// `http://localhost:8080/search/name_group/123`
$app->get('/search/:group/:id', function($group, $id, Request $req) {
    return "Group / ID: " . $group . '/' . $id;  // Group / ID: name_group/123
});
```

`/users/(\d+)` is a route pattern that matches URLs like `/users/123`, where `123` is the user ID passed to the callback.

### Callback Formats
Various callback formats are accepted:

1. **Array Callback:**

    The callback should be an array with two elements, where the first element is the controller class name and the second is the method name.

    ```php
    $app->get('/array', [HomeController::class, 'index']);
    ```

2. **String Callback:**

    The callback should be a string in the format `'Controller@method'`, where `Controller` is the controller class name and `method` is the method name.

    ```php
    $app->get('/string', 'HomeController@index');
    ```

3. **Callable Callback:**

    The callback should be a callable, such as a closure or an anonymous function, or a function defined as callable.

    ```php
    $app->get('/anonymous-function', function() {
        return "Hello World";
    });
    
    $app->get('/arrow-function', fn() => "Hello World");
    ```
#### Summary

- **Array:** `[ControllerClass::class, 'methodName']`
- **String:** `'Controller@method'`
- **Callable:** `function(Request $req) { ... }` or `fn(Request $req) => ...`

### Implementing Middleware
You can add middleware to your routes by passing them as additional parameters to the get method.

```php
// Middleware example
class AuthMiddleware implements Middleware {
    public function execute(Request $request, callable $next)
        // Authentication logic
        if (!$request->user) {
            return redirect('/login');
        }
        
        return $next($req);
}

// Applying middleware to a route
//$app->get(string $path, $callback, ...$middlewares): void
$app->get('/dashboard', 'DashboardController@index', AuthMiddleware::class);

```