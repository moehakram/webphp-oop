<?php

use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Application;
use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Http\Responses\Response;
use MA\PHPQUICK\Http\Responses\ResponseHeaders;

if (!function_exists('log_exception')) {
    function log_exception(\Throwable $ex): void
    {
        $time = date('Y-m-d H:i:s');
        $message = "[{$time}] Uncaught exception: " . $ex->getMessage() . "\n";
        $message .= "In file: " . $ex->getFile() . " on line " . $ex->getLine() . "\n";
        $message .= "Stack trace:\n" . $ex->getTraceAsString() . "\n";
        error_log($message, 3, __DIR__ . '/../logs/errors.log');
    }
}

if (!function_exists('write_log')) {
    function write_log($message, $filename = 'app.log')
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] " . (is_array($message) ? json_encode($message) : $message) . PHP_EOL;
        file_put_contents(rtrim(config('dir.logs'), '/') . '/' . $filename, $logMessage, FILE_APPEND);
    }
}

if (!function_exists('session')) {

    function session($key = null, $default = null)
    {
        return Application::session()->getOrSet($key, $default);
    }
}

if (!function_exists('response')) {
    function response($content = null, $statusCode = 200): Response
    {
        $res = Application::response();
        if ($content !== null) {
            $res->setContent($content)->setStatusCode($statusCode);
        }
        return $res;
    }
}

if (!function_exists('request')) {
    function request(): Request
    {
        return Application::request();
    }
}

if (!function_exists('headers')) {
    function headers(): ResponseHeaders
    {
        return response()->headers();
    }
}

if (!function_exists('strRandom')) {
    function strRandom(int $length = 16): string
    {
        return (function ($length) {
            $string = '';

            while (($len = strlen($string)) < $length) {
                $size = $length - $len;

                $bytesSize = (int) ceil($size / 3) * 3;

                $bytes = random_bytes($bytesSize);

                $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
            }

            return $string;
        })($length);
    }
}

// if(!function_exists('set_CSRF')){
//     function set_CSRF(string $path): string
//     {
//         $token = strRandom(17);
//         // response()->setCookie('csrf_token', $token, time() + 60 * 60 * 30, $path);
//         response()->headers()->setCookie(new Cookie('csrf_token', $token, time()+3600));
//         return $token;
//     }
// }

if (!function_exists('csrf')) {
    function csrf(): string
    {
        session()->set('token', $token = bin2hex(random_bytes(35)));
        return $token;
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = [], ?string $layout = null): View
    {
        return new View($view, $data, $layout);
    }
}

if (!function_exists('PHPQuick')) {
    function PHPQuick(array $config)
    {
        static $app = null;

        if (is_null($app)) {
            $app = new Application($config);
        }

        return $app;
    }
}

if (!function_exists('config')) {
    function config($key = null, $default = null)
    {
        return Application::config()->getOrSet($key, $default);
    }
}
if (!function_exists('clean')) {
    function clean($data)
    {
        if (is_string($data)) {
            return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
        }

        if (is_array($data)) {
            return array_map('clean', $data);
        }
        return $data;
    }
}

if (!function_exists('dd')) {
    function dd($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die;
    }
}

if (!function_exists('displayAlert')) {
    function displayAlert(array $value): string
    {
        return sprintf(
            '<div class="alert alert-%s">%s</div>',
            $value['type'],
            $value['message']
        );
    }
}
if (!function_exists('inputs')) {
    function inputs($key)
    {
        return session()->getFlash('inputs')[$key] ?? '';
    }
}
if (!function_exists('errors')) {
    function errors($key)
    {
        return session()->getFlash('errors')[$key] ?? '';
    }
}
