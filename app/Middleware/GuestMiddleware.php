<?php

namespace App\Middleware;

use App\Service\ServiceTrait;
use MA\PHPQUICK\Interfaces\Middleware;
use MA\PHPQUICK\Interfaces\Request;

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
