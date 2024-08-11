<?php
namespace MA\PHPQUICK\Contracts;

use MA\PHPQUICK\Http\RequestInterface;

interface Middleware
{
    public function execute(RequestInterface $request, \Closure $next);
}
