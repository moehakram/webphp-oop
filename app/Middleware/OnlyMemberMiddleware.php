<?php

namespace App\Middleware;

use MA\PHPQUICK\Interfaces\Middleware;
use MA\PHPQUICK\Interfaces\Request;

class OnlyMemberMiddleware implements Middleware
{
    public function execute(Request $request, callable $next)
    {
        $user = $request->user();
        if ($user == null) {
            response()->redirect('/user/login');
        }
        return $next($request);
    }
}
