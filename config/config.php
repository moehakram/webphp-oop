<?php
declare(strict_types=1);

return [
    // Konfigurasi umum aplikasi
    'app' => [
        'name' => 'PHPQuick',
        'url'  => 'http://www.localhost:8080/',
    ],

    // Konfigurasi database
    'database' => [
        'driver'   => 'mysql',
        'host'     => '127.0.0.1',
        'port'     => '3306',
        'dbname'     => 'php_mvc',
        'charset'  => 'utf8mb4',
        'username' => 'root',
        'password' => '',
    ],

    // Konfigurasi error page custom
    'error_pages' => [
        '403' => 'error.403', // Halaman untuk error 403 Forbidden
        '404' => 'error.404', // Halaman untuk error 404 Not Found
        '500' => 'error.500', // Halaman untuk error 500 Internal Server Error
    ],

    // Konfigurasi logging
    'logging' => [
        'error_log' => [
            'path' => 'logs/error.log',
        ],
        'info_log' => [
            'path' => 'logs/app.log',
        ],
    ],
];
