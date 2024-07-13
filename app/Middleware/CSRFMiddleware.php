<?php

namespace App\Middleware;

use MA\PHPQUICK\Exception\HttpException;
use MA\PHPQUICK\Interfaces\Middleware;
use MA\PHPQUICK\Interfaces\Request;

class CSRFMiddleware implements Middleware
{
    public function execute(Request $request, callable $next)
    {
        if ($request->getMethod() == 'POST') {
            $token = $request->getPost()->get('csrf_token', '');
            if ($token === $request->getCookies()->get('csrf_token')) return $next($request);
        }

        // return response()->setNotFound('CSRF_TOKEN tidak valid !');
        throw new HttpException(400, 'CSRF_TOKEN tidak valid !');
    }
}
