<?php

use MA\PHPQUICK\Container;

require __DIR__ . '/../vendor/autoload.php';

$bootstrap = require base_path('bootstrap.php');
$container = new Container;
Container::$instance = $container;
$bootstrap->boot($container);

// 1. memanggil fungsi global

function greet($name) {
    return "Hello, $name!";
}

// Memanggil fungsi global dengan parameter
$result = $container->call('greet', ['name' => '1. Memanggil fungsi global dengan parameter']);
print $result . PHP_EOL; // Hello, 1. Memanggil fungsi global dengan parameter!


// 2. Memanggil Method dari Sebuah Kelas

class Greeter {
    public function sayHello($name) {
        return "Hello, $name!";
    }
}
$container->bind(Greeter::class, fn() => new Greeter());

// // Memanggil method dari instance kelas
$result = $container->call([Greeter::class, 'sayHello'], ['name' => '2. Memanggil method dari instance kelas']);
print $result . PHP_EOL; // Output: Hello, Bob!


// 3. Memanggil Metode Static dari Sebuah Kelas

class Utils {
    public static function makeTitle($string) {
        return strtoupper($string);
    }
}

// Memanggil metode statis
$result = $container->call([Utils::class, 'makeTitle'], ['string' => '3. Memanggil Metode Static dari Sebuah Kelas']);
echo $result . PHP_EOL; // Output: HELLO WORLD


// 4. Memanggil Closure dengan Dependency Injection

class Logger {
    public function log($message) {
        echo "Log: $message" . PHP_EOL;
    }
}
$container->bind(Logger::class, fn() => new Logger());

$closure = function (Logger $logger, $message) {
    $logger->log($message);
};

// Memanggil closure dengan dependency injection
$container->call($closure, ['message' => '4. Memanggil Closure dengan Dependency Injection']); 
// Output: Log: This is a log message

// 5. Menggunakan Dependency Injection pada Method Instance

class ReportGenerator {
    protected $logger;
    
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    public function generate($type) {
        $this->logger->log("Generating report of type: $type");
    }
}
$container->bind(Logger::class, fn() => new Logger());
$container->bind(ReportGenerator::class, fn($c) => new ReportGenerator($c->get(Logger::class)));

// Memanggil method generate pada instance dari ReportGenerator
$container->call([ReportGenerator::class, 'generate'], ['type' => '5. Menggunakan Dependency Injection pada Method Instance']);
// Output: Log: Generating report of type: monthly


// 6. Menggunakan Metode Callback Dinamis

class UserManager {
    public function create($name, $email) {
        return "6. Menggunakan Metode Callback Dinamis . User $name with email $email created!";
    }
}
$container->bind(UserManager::class, fn() => new UserManager());
// Menggunakan callable dinamis
$callback = [UserManager::class, 'create'];
$result = $container->call($callback, ['name' => 'John', 'email' => 'john@example.com']);
echo $result . PHP_EOL; // Output: User John with email john@example.com created!



// 7. Memanggil Fungsi dengan Parameter Tipe Tertentu

class DatabaseConnection {
    public function connect() {
        return '7. Memanggil Fungsi dengan Parameter Tipe Tertentu . Connected to database';
    }
}
$container->bind(DatabaseConnection::class, fn() => new DatabaseConnection());

$closure = function (DatabaseConnection $db) {
    return $db->connect();
};

// Memanggil fungsi yang menerima parameter tipe tertentu
$result = $container->call($closure);
echo $result . PHP_EOL; // Output: Connected to database


// 8. Memanggil Fungsi dengan Parameter Opsional

$closure = function ($message = 'Hello, World!') {
    return $message;
};

// Memanggil closure tanpa parameter, menggunakan default value
$result = $container->call($closure);
echo $result . PHP_EOL; // Output: Hello, World!
