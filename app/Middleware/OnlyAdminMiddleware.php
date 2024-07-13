<?php

namespace App\Middleware;

use MA\PHPQUICK\Exception\HttpException;
use MA\PHPQUICK\Interfaces\Middleware;
use MA\PHPQUICK\Interfaces\Request;

class OnlyAdminMiddleware implements Middleware
{
    public function execute(Request $request, callable $next)
    {
        $user = $request->user();

        if ($this->isAdmin($user)) {
            return $next($request);
        }
        throw new HttpException(403);
    }

    private function isAdmin($user): bool
    {
        return $user !== null && $user->getRole() == 1;
    }
}
