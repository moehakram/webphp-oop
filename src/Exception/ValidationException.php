<?php
namespace MA\PHPQUICK\Exception;

use MA\PHPQUICK\Collection;

class ValidationException extends \Exception
{
    private Collection $errors;

    public function __construct(string $message = "Validation Error", Collection $errors = null)
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): Collection
    {
        return $this->errors;
    }
}
