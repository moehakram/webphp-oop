<?php

namespace App\Middleware;

use MA\PHPQUICK\Interfaces\Request;
use MA\PHPQUICK\Interfaces\Middleware;
use App\Service\ServiceTrait;

class AuthMiddleware implements Middleware{

    use ServiceTrait;

    public function __construct()
    {
        $this->authService();
    }

    public function execute(Request $request, callable $next)
    {
        $user = $this->sessionService->current();
        if ($user == null) {
            response()->redirect('/user/login');
        }

        $request->login($user);
        
        return $next($request);
    }
}