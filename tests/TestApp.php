<?php

use App\Domain\User;
use MA\PHPQUICK\Application;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application(require base_path('config/config.php'));

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

$app->bind(User::class, function(){
    return User::class;
});

// var_dump($app->resolve(User::class));

var_dump(app(User::class));