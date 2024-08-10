<?php

declare(strict_types=1);

use MA\PHPQUICK\Application;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Http\Requests\Request;

// Include the autoloader to load necessary classes
require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrapping
$bootstrap = require __DIR__ . '/../bootstrap.php';

// Create and configure the router
$router = new Router();
require __DIR__ . '/../config/routes.php';

// Boot and run the application
$bootstrap->boot(
    new Application(
        request: new Request(),
        router: $router
    )
)->run();
