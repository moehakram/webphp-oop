<?php
declare(strict_types=1);

use MA\PHPQUICK\Config;
use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Bootstrap;
use App\Service\UserService;
use App\Service\SessionService;
use App\Middleware\AuthMiddleware;
use App\Middleware\CSRFMiddleware;
use App\Repository\UserRepository;
use App\Middleware\GuestMiddleware;
use App\Repository\SessionRepository;
use App\Middleware\OnlyAdminMiddleware;
use App\Middleware\CurrentUserMiddleware;
use MA\PHPQUICK\Contracts\ContainerInterface as App;
use MA\PHPQUICK\Session\Session;

return (new Bootstrap(

    // Handler untuk menangani pengecualian (exception)
    exceptionHandler: function(\Throwable $ex){
        // Log exception ke sistem log
        log_exception($ex);
        // Set kode respon HTTP menjadi 500 (Internal Server Error)
        http_response_code(500);
        // Menampilkan halaman error 500 menggunakan view
        echo View::error_500('errors', 'Whoops, looks like something went wrong!');
    },
    initializeSession: function(App $app){
        $app->instance(Session::class, $session = new Session);
        $app->instance('session', $session); // alias session
    },
    
    // Alias untuk middleware yang bisa digunakan dalam route (opsional)
    middlewareAliases: function(): array {
        return [
            'auth' => AuthMiddleware::class, // Middleware untuk otentikasi
            'admin' => OnlyAdminMiddleware::class, // Middleware untuk akses admin
            'guest' => GuestMiddleware::class, // Middleware untuk tamu yang belum login
            'csrf' => CSRFMiddleware::class, // Middleware untuk perlindungan CSRF
        ];
    },

    // Middleware global yang akan dijalankan pada setiap request
    middlewareGlobal: function (): array {
        return [
            CurrentUserMiddleware::class // Middleware untuk mengatur pengguna saat ini
        ];
    },
    // Inisialisasi pengaturan database
    initializeDatabase: function (\PDO $pdo): void {
        // Mengatur atribut PDO mode fetch default
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    },

    // Inisialisasi layanan (services) yang akan di-manage oleh container
    initializeServices: function (App $app): void {
        $app->singleton(SessionService::class, function (App $app) {
            return new SessionService(
                $app->get(SessionRepository::class), 
                $app->get(UserRepository::class),
                $app->get(Session::class)
            );
        });

        $app->singleton(UserService::class, function (App $app) {
            return new UserService($app->get(UserRepository::class));
        });
    },

    // Inisialisasi repositories yang akan di-manage oleh container
    initializeRepositories: function (App $app): void {
        $app->singleton(SessionRepository::class, function (App $app) {
            return new SessionRepository($app->get(\PDO::class));
        });
        $app->singleton(UserRepository::class, function (App $app) {
            return new UserRepository($app->get(\PDO::class));
        });
    },

    // Inisialisasi konfigurasi aplikasi (opsional)
    // function (Config $config) : void {}
    initializeConfig: null,

    // Inisialisasi domain logika aplikasi (opsional)
    // function (App $app) : void {}
    initializeDomain:null,
    
    // Handler untuk menangani HTTP Exception (opsional)
    // function (HttpExceptionInterface $httpException) : Response {}
    httpExceptionHandler:null,
    
    // Hook custom bootstrap (opsional)
    // function (App $app) : void {}
    customBoot:null
));