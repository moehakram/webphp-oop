<?php

namespace App\Middleware;

use MA\PHPQUICK\Http\RequestInterface as Request;
use MA\PHPQUICK\Interfaces\Middleware;

class AuthMiddleware implements Middleware{

    public function execute(Request $request, \Closure $next)
    {
        if ($request->user() == null) {
            return response()->redirect('/users/login');
        }
        
        return $next($request);
    }
}