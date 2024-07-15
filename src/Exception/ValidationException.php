<?php
namespace MA\PHPQUICK\Exception;

use MA\PHPQUICK\Errors;

class ValidationException extends \Exception
{
    private Errors $errors;

    public function __construct(string $message = "Validation Error", Errors $errors = null)
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): Errors
    {
        return $this->errors;
    }
}
