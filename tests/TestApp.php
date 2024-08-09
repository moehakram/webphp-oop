<?php

use App\Domain\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use MA\PHPQUICK\Application;
use MA\PHPQUICK\Database\Database;

require __DIR__ . '/../vendor/autoload.php';

// $app = new Application(require base_path('config/config.php'));

function tesConfig(){
    config()->set('database.password', 'akram');
    
    dd(config());
}

function tesUser(){
    $user = new User;
    $user->name = 'akram';
    request()->login($user);
    dd(request()->user());
}


// tesConfig();
// tesUser();

function testLog(){
    write_log('tes aja', 'tes');
}

// testLog();

function testContainer($app){
    // $app->singleton(User::class, function(){
    //     return new User;
    // });
    
    // $app->bind(User::class, function(){
    //     return new User;
    // });
    
    
    // var_dump($app->resolve(User::class));
    $app->instance(User::class, new User);
    
    var_dump(app()->fetch(User::class) === app(User::class));
}

// testContainer($app);

function testDependensiInjection($app){


    $app->singleton(\PDO::class, function(){
        return Database::getConnection();
    });
    // var_dump($app->fetch(\PDO::class) === Database::getConnection());die;
    
    $app->singleton(UserRepository::class, function($container){
        return new UserRepository($container->fetch(\PDO::class));
    });
    
    $app->instance(UserService::class, $us = $app->fetch(UserService::class));
    
    
    var_dump($app->fetch(UserService::class) === $us);
}

// testDependensiInjection($app);

function testArr(...$arguments){
    return is_array($arguments[0]) 
    ? $arguments[0] 
    : [$arguments[0] => $arguments[1] ?? null];
}

var_dump(testArr(['title' => 'Dashboard']));
var_dump(testArr('title'));
var_dump(testArr('title', 'dashboard'));