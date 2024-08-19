<?php
declare(strict_types=1);

// Include the autoloader to load necessary classes
require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrapping
$bootstrap = require __DIR__ . '/../bootstrap.php';

// Create and configure the router
$router = new \MA\PHPQUICK\Router\Router();
require __DIR__ . '/../config/routes.php';

// Boot and run the application
$bootstrap->boot(
    new \MA\PHPQUICK\Application(
        router: $router,
        basePath: dirname(__DIR__),
        config: new \MA\PHPQUICK\Config(require __DIR__ . '/../config/config.php')
    )
)->run()
->send();