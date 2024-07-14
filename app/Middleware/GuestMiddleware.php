<?php

namespace App\Middleware;

use App\Service\ServiceTrait;
use MA\PHPQUICK\Interfaces\Middleware;
use MA\PHPQUICK\Interfaces\Request;

class GuestMiddleware implements Middleware
{
    use ServiceTrait;

    public function __construct()
    {
        $this->authService();
    }

    public function execute(Request $request, callable $next)
    {
        $user = $this->sessionService->current();
        if ($user != null) {
            return response()->redirect('/');
        }
        return $next($request);
    }
}
