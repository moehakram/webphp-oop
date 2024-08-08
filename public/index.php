<?php

// Include the autoloader to load necessary classes
require_once __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/../config/routes.php';

$app->run();