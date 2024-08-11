<?php

namespace App\Middleware;

use MA\PHPQUICK\Contracts\Middleware;
use MA\PHPQUICK\Http\RequestInterface as Request;

class CSRFMiddleware implements Middleware
{
    public function execute(Request $request, \Closure $next)
    {
        if ($request->getMethod() == 'POST') {
            $token = $request->post('csrf_token', '');
            if ($token === $request->session()->get('token')){
                $request->session()->remove('token');
                return $next($request);
            }
        }

        return response()->setNotFound('CSRF_TOKEN tidak valid !');
    }
}
