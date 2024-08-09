<?php
declare(strict_types=1);

use MA\PHPQUICK\Application;
use MA\PHPQUICK\Router\Router;
use MA\PHPQUICK\Http\Requests\Request;

// Include the autoloader to load necessary classes
require_once __DIR__ . '/../vendor/autoload.php';

$router = new Router;

require __DIR__ . '/../config/routes.php';

(new Application(
    container: require __DIR__ . '/../bootstrap.php',
    request: new Request,
    router: $router
))->run();