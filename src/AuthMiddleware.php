<?php

namespace MA\PHPQUICK;

use App\Service\SessionService;
use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Interfaces\Middleware;

class AuthMiddleware implements Middleware{

    public function execute(Request $request, \Closure $next)
    {
        $user = app(SessionService::class)->current();
        $request->login($user);
        return $next($request);
    }
}