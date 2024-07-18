<?php
namespace MA\PHPQUICK\MVC;

use MA\PHPQUICK\Validation\Collection;
use MA\PHPQUICK\Validation\Validation;

abstract class Model extends Validation
{
    public function __construct(array $data = [])
    {
        $this->loadData($data);
        parent::__construct();
    }

    public function validate($validationRules = []) : Collection
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