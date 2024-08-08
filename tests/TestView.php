<?php

use MA\PHPQUICK\Application;
use MA\PHPQUICK\MVC\View;

require __DIR__ . '/../vendor/autoload.php';
$app = new Application(require __DIR__ . '/../config/config.php');

$view = __DIR__ . '/../app/views';
// $result = View::layouts_app(['title' => 'akram']);
$result = View::error_500();

dd($result);