<?php

namespace MA\PHPQUICK;

use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Interfaces\Middleware;
use App\Service\ServiceTrait;

class AuthMiddleware implements Middleware{

    use ServiceTrait;

    public function __construct()
    {
        $this->authService();
    }

    public function execute(Request $request, \Closure $next)
    {
        $user = $this->sessionService->current();
        $request->login($user);
        return $next($request);
    }
}