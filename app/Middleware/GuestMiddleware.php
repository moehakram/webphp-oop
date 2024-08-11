<?php

namespace App\Middleware;

use MA\PHPQUICK\Contracts\Middleware;
use MA\PHPQUICK\Http\RequestInterface as Request;

class GuestMiddleware implements Middleware
{
    public function execute(Request $request, \Closure $next)
    {
        if ($request->user() != null) {
            return response()->redirect('/');
        }
        return $next($request);
    }
}
