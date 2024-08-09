<?php

use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use App\Service\SessionService;
use App\Service\UserService;
use MA\PHPQUICK\Application;
use MA\PHPQUICK\Database\Database;
use MA\PHPQUICK\MVC\View;

set_exception_handler(function (\Throwable $ex) {
    log_exception($ex);
    $errors = 'Whoops, looks like something went wrong!';
    echo View::error_500(compact('errors'))->render();
});

$app = new Application(require __DIR__ . '/config/config.php');

$app->singleton(SessionRepository::class, function(){
    return new SessionRepository(Database::getConnection());
});

$app->singleton(SessionService::class, function($app){
    return new SessionService($app->resolve(SessionRepository::class));
});

$app->singleton(UserRepository::class, function(){
    return new UserRepository(Database::getConnection());
});

$app->singleton(UserService::class, function($app){
    return new UserService($app->resolve(UserRepository::class));
});

return $app;