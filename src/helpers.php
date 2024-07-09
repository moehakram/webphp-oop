<?php

use MA\PHPQUICK\Config;
use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Application;
use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Interfaces\Response;

if(!function_exists('cetak')){
    function cetak($arr, $die = true)
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    
        if ($die) {
            die;
        }
    }
}

if(!function_exists('response')){
    function response($content = '', $statusCode = 200): Response
    {
        $res = Application::$app->response;
        if($content !== ''){
            $res->setContent($content)->setStatusCode($statusCode);
        }
        return $res;
    }
}

if(!function_exists('request')){
    function request(): Request
    {
        return Application::$app->request;
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

if(!function_exists('set_CSRF')){
    function set_CSRF(string $path): string
    {
        $token = strRandom(17);
        response()->setCookie('csrf_token', $token, time() + 60 * 60 * 30, $path);
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
    function config($key_name, $default = null){
        return Config::get($key_name, $default);
    }
}