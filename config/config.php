<?php

return [
    'app.url' => 'http://www.localhost:8080/',
    'database' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'php_mvc',
        'username' => 'root',
        'password' => ''
    ],
    'httpErrorPage' => [
        '403' => 'error.403', // status code => page view
        '404' => 'error.404',
        '500' => 'error.405'
    ]
];
