<?php

namespace App\Middleware;

use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Interfaces\Middleware;
use App\Service\ServiceTrait;

class AuthMiddleware implements Middleware{

    use ServiceTrait;

    public function execute(Request $request, callable $next)
    {
        $this->authService();
        $request->login($this->sessionService->current());
        return $next($request);
    }
}