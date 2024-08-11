<?php
declare(strict_types=1);

use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Bootstrap;
use App\Service\UserService;
use App\Service\SessionService;
use MA\PHPQUICK\Session\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\CSRFMiddleware;
use App\Repository\UserRepository;
use App\Middleware\GuestMiddleware;
use App\Repository\SessionRepository;
use App\Middleware\OnlyAdminMiddleware;
use MA\PHPQUICK\Contracts\ExtendedContainerInterface as App;

// Set exception handler
set_exception_handler(function (\Throwable $ex) {
    log_exception($ex);
    http_response_code(500);
    echo View::error_500('errors', 'Whoops, looks like something went wrong!');
});

return (new Bootstrap(
    // initializeErrorViews: function (App $app): void {
    //     // Custom error view logic
    //     $app->bind('404', fn() => View::make('error.404'));
    //     $app->bind('403', fn() => View::make('error.403'));
    //     $app->bind('500', fn() => View::make('error.500'));
    // },
    initializeErrorViews: function (App $app): array {
        // Custom error view logic
        return [
            '404' => fn() => View::make('error.404'),
            '403' => fn() => View::make('error.403'),
            '500' => fn() => View::make('error.500')
        ];
    },
    middlewareAliases: function(): array {
        return [
            'auth' => AuthMiddleware::class,
            'admin' => OnlyAdminMiddleware::class,
            'guest' => GuestMiddleware::class,
            'csrf' => CSRFMiddleware::class
        ];
    },
    initializeSession:function(App $app){
        $app->instance(Session::class, new Session());
    },
    // initializeConfig: function (Config $config): void {
    //     $config->set('author', 'akram');
    // },
    // initializeDatabase: function (\PDO $pdo): void {
    //     // Uncomment if you want to set PDO attributes
    //     $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    // },
    initializeServices: function (App $app): void {
        $app->singleton(SessionService::class, function (App $app) {
            return new SessionService($app->get(SessionRepository::class));
        });
        $app->singleton(UserService::class, function (App $app) {
            return new UserService($app->get(UserRepository::class));
        });
    },
    initializeRepositories: function (App $app): void {
        // Uncomment to use container-bound PDO instance
        $app->singleton(SessionRepository::class, function (App $app) {
            return new SessionRepository($app->get(\PDO::class));
        });
        $app->singleton(UserRepository::class, function (App $app) {
            return new UserRepository($app->get(\PDO::class));
        });
    },
));