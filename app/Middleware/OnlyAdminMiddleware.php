<?php

namespace App\Middleware;

use App\Domain\User;
use MA\PHPQUICK\Contracts\Middleware;
use MA\PHPQUICK\Contracts\RequestInterface as Request;

class OnlyAdminMiddleware implements Middleware
{
    public function execute(Request $request, \Closure $next)
    {
        $user = $request->user();

        if ($this->isAdmin($user)) {
            return $next($request);
        }
        response()->setForbidden();
    }

    private function isAdmin(?User $user): bool
    {
        return $user !== null && $user->role == 1;
    }
}
