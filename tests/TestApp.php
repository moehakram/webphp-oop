<?php

use App\Domain\User;
use MA\PHPQUICK\Application;

require __DIR__ . '/../vendor/autoload.php';

$app = new Application(require __DIR__ . '/../config/config.php');

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
tesUser();