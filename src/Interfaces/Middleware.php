<?php

namespace MA\PHPQUICK\Interfaces;

use MA\PHPQUICK\Interfaces\Request;

interface Middleware
{
    public function execute(Request $request, callable $next);
}
