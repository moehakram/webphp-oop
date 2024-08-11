<?php

namespace App\Middleware;

use MA\PHPQUICK\Contracts\Middleware;
use MA\PHPQUICK\Http\RequestInterface as Request;

class AuthMiddleware implements Middleware{

    public function execute(Request $request, \Closure $next)
    {
        if ($request->user() == null) {
            return response()->redirect('/users/login');
        }
        
        return $next($request);
    }
}