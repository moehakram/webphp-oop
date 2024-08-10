<?php
declare(strict_types=1);

use MA\PHPQUICK\Config;
use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Bootstrap;
use App\Service\UserService;
use MA\PHPQUICK\Application;
use App\Service\SessionService;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;

// Set exception handler
set_exception_handler(function (\Throwable $ex) {
    log_exception($ex);
    http_response_code(500);
    echo View::error_500('errors', 'Whoops, looks like something went wrong!');
});

return (new Bootstrap(
    initializeErrorViews: function (Application $app): void {
        // Custom error view logic
        $app->bind('404', fn() => View::make('error.404'));
        $app->bind('403', fn() => View::make('error.403'));
        $app->bind('500', fn() => View::make('error.500'));
    },
    // initializeConfig: function (Config $config): void {
    //     $config->set('author', 'akram');
    // },
    // initializeDatabase: function (\PDO $pdo): void {
    //     // Uncomment if you want to set PDO attributes
    //     $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    // },
    initializeServices: function (Application $app): void {
        $app->singleton(SessionService::class, function (Application $app) {
            return new SessionService($app->get(SessionRepository::class));
        });
        $app->singleton(UserService::class, function (Application $app) {
            return new UserService($app->get(UserRepository::class));
        });
    },
    initializeRepositories: function (Application $app): void {
        // Uncomment to use container-bound PDO instance
        $app->singleton(SessionRepository::class, function (Application $app) {
            return new SessionRepository($app->get(\PDO::class));
        });
        $app->singleton(UserRepository::class, function (Application $app) {
            return new UserRepository($app->get(\PDO::class));
        });
    },
));