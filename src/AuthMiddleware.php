<?php

namespace MA\PHPQUICK;

use App\Service\SessionService;
use MA\PHPQUICK\Contracts\Middleware;
use MA\PHPQUICK\Http\RequestInterface;

class AuthMiddleware implements Middleware{

    public function execute(RequestInterface $request, \Closure $next)
    {
        $user = app(SessionService::class)->current();
        $request->login($user);
        return $next($request);
    }
}