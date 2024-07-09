<?php

return [
    'app' => [
        'url' => 'http://www.localhost:8080/'
    ],

    'dir' => [
        'views' => dirname(__DIR__) . '/app/views/'
    ],

    'database' => [
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'php_mvc',
        'username' => 'root',
        'password' => ''
    ],

    'session' => [
        'name' => 'PHP-MVC',
        'key' => "kRd9SO75b0MffA6ThNjW0lYfZpUJzwbiwN9moDf0wQvyLWmBdrnYbCZ4IekHQVNenFD8gt4sKreL7Z",
        'exp' => time() + (60 * 60 * 3) // 3 JAM
    ]
];
