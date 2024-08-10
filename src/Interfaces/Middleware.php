<?php
namespace MA\PHPQUICK\Interfaces;

use MA\PHPQUICK\Http\RequestInterface;

interface Middleware
{
    public function execute(RequestInterface $request, \Closure $next);
}
