<?php

namespace App\Middleware;

use MA\PHPQUICK\Contracts\Middleware;
use MA\PHPQUICK\Contracts\RequestInterface as Request;

class CSRFMiddleware implements Middleware
{
    public function execute(Request $request, \Closure $next)
    {
        $token = $request->input('csrf_token', '');
        if (hash_equals($request->session()->get('token', ''), $token)){
            $request->session()->remove('token');
            return $next($request);
        }

        return response()->back();
    }
}
