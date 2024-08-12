<?php

namespace App\Middleware;

use App\Service\SessionService;
use MA\PHPQUICK\Contracts\Middleware;
use MA\PHPQUICK\Contracts\RequestInterface;

class CurrentUserMiddleware implements Middleware{

    public function execute(RequestInterface $request, \Closure $next)
    {
        $user = app(SessionService::class)->current();
        $request->login($user); // jika login, data user tersimpan di objek request  dan jika tidak login maka data null 
        return $next($request);
    }
}