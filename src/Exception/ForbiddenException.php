<?php

namespace MA\PHPQUICK\Exception;

class ForbiddenException extends \Exception
{
    protected $message = 'You don\'t have access permission to access this page';
    protected $code = 403;
}