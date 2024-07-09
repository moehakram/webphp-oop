<?php

namespace MA\PHPQUICK\Exception;

class NotFoundException extends \Exception
{
    protected $message = 'Not Found';
    protected $code = 404;
}