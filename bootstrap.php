<?php
declare(strict_types=1);

use MA\PHPQUICK\Config;
use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Container;
use App\Service\UserService;
use App\Service\SessionService;
use MA\PHPQUICK\AuthMiddleware;
use App\Repository\UserRepository;
use MA\PHPQUICK\Database\Database;
use App\Repository\SessionRepository;

set_exception_handler(function (\Throwable $ex) {
    log_exception($ex);
    http_response_code(500);
    echo View::error_500('errors', 'Whoops, looks like something went wrong!');
});

$container = new Container();

// Configuration
$container->instance('config', new Config(require __DIR__ . '/config/config.php'));

// Database Connection
$container->singleton(\PDO::class, fn() => Database::getConnection());

// Repositories
$container->singleton(SessionRepository::class, fn(Container $container) => 
    new SessionRepository($container->get(\PDO::class))
);
$container->singleton(UserRepository::class, fn(Container $container) => 
    new UserRepository($container->get(\PDO::class))
);

// Services
$container->singleton(SessionService::class, fn(Container $container) => 
    new SessionService($container->get(SessionRepository::class))
);
$container->singleton(UserService::class, fn(Container $container) => 
    new UserService($container->get(UserRepository::class))
);

// Global Middleware
$container->instance('middlewareGlobal', [AuthMiddleware::class]);

return $container;