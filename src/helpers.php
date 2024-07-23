<?php

use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Application;
use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Http\Responses\Response;
use MA\PHPQUICK\Session\Session;

set_exception_handler(function(\Throwable $ex) {

    $time = date('Y-m-d H:i:s');
    $message = "[{$time}] Uncaught exception: " . $ex->getMessage() . "\n";
    $message .= "In file: " . $ex->getFile() . " on line " . $ex->getLine() . "\n";
    $message .= "Stack trace:\n" . $ex->getTraceAsString() . "\n";

    // Menulis log ke file
    error_log($message, 3, __DIR__ . '/../logs/errors.log');

    // Mengeluarkan pesan ke pengguna
    echo 'Whoops, looks like something went wrong!';
});

if(!function_exists('app')){
    function app() : Application
    {
        return Application::$app;
    }
}

if(!function_exists('session')){
    function session() : Session
    {
        return app()->session;
    }
}

if(!function_exists('response')){
    function response($content = '', $statusCode = 200): Response
    {
        $res = app()->response;
        if($content !== ''){
            $res->setContent($content);
            $res->setStatusCode($statusCode);
        }
        return $res;
    }
}

if(!function_exists('request')){
    function request(): Request
    {
        return app()->request;
    }
}

if(!function_exists('strRandom')){
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

if(!function_exists('csrf')){
    function csrf(): string
    {  
        session()->set('token', $token = bin2hex(random_bytes(35)));
        return $token;
    }
}

if(!function_exists('view')){
    function view(string $view, array $data = [], string $extends = '')
    {
        return View::render($view, $data, $extends);
    }
}

if(!function_exists('PHPQuick')){
    function PHPQuick(array $config)
    {
        return new Application($config);
    }
}

if(!function_exists('config')){
    function config($key = '', $default = null){
        if($key !== '' && $key !== null){
            return Application::$app->config->get($key, $default);
        }

        return Application::$app->config;
    }
}
if(!function_exists('clean')){
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

function d($data)
{
	echo '<pre>';
	var_dump($data);
	echo '</pre>';
}

function dd($data, $callback = 'print_r'){
    echo '<pre>';
    $callback($data);
    echo '</pre>';
    die;
}