<?php
namespace MA\PHPQUICK\MVC;

use MA\PHPQUICK\Interfaces\ValidatorInterface;
use MA\PHPQUICK\Validation\ErrorsValidation;
use MA\PHPQUICK\Validation\Validation;

abstract class Model extends Validation
{
    public function __construct($data = [])
    {
        $this->loadData($data);
        parent::__construct();
    }

    public function validate($validationRules = []) : ErrorsValidation
    {
        parent::validate();
        return $this->getErrors();
        
    }

    public function has(string $key): bool
    {
        return isset($this->$key);
    }

    public function get(string $key, $default = null)
    {
        return $this->$key ?? $default;
    }

    public function set(string $key, $value)
    {
        if (property_exists($this, $key)) {
            $this->$key = $value;
        }
    }
}