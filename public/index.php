<?php

use MA\PHPQUICK\Application;
use MA\PHPQUICK\MVC\View;

set_exception_handler(function (\Throwable $ex) {
    log_exception($ex);
    $errors = 'Whoops, looks like something went wrong!';
    echo View::render('error.500',compact('errors'));
});

// Include the autoloader to load necessary classes
require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$app = new Application($config);

require __DIR__ . '/../config/routes.php';

$app->run();