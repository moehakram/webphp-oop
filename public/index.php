<?php

// Include the autoloader to load necessary classes
require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

$app = PHPQuick($config);

require __DIR__ . '/../config/routes.php';

$app->run();
