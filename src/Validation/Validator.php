<?php
namespace MA\PHPQUICK\Validation; // @link https://www.phptutorial.net/php-tutorial/php-validation/

use MA\PHPQUICK\Collection;
use MA\PHPQUICK\Interfaces\ValidatorInterface;

abstract class Validator extends ValidationMethods implements ValidatorInterface
{
    const DEFAULT_ERROR_MESSAGES = [
        // required
        'required' => 'Please enter the %s',
        // email
        'email' => 'The %s is not a valid email address',
        // min:number
        'min' => 'The %s must have at least %s characters',
        // max:number 
        'max' => 'The %s must have at most %s characters',
        // between:min,max
        'between' => 'The %s must have between %d and %d characters',
        // same:field_other
        'same' => 'The %s must match with %s',
        // alphanumeric
        'alphanumeric' => 'The %s should have only letters and numbers',
        //secure
        'secure' => 'The %s must have between 8 and 64 characters and contain at least one number, one upper case letter, one lower case letter and one special character',
        // unique:tabel,column
        'unique' => 'The %s already exists',
        // numeric
        'numeric' => 'The %s must be a numeric value'
    ];

    protected Collection $errors;

    public function __construct()
    {
        $this->errors = new Collection();
    }

    abstract public function rules(): array;

    public function errorMessages(): array
    {
       return [];
    }

    public function validate(): bool
    {
        $split = fn($str, $separator) => array_map('trim', explode($separator, $str));

        $rule_messages = array_filter($this->errorMessages(), fn($message) => is_string($message));
        $validation_errors = array_merge(self::DEFAULT_ERROR_MESSAGES, $rule_messages);

        foreach ($this->rules() as $field => $option) {
            $rules = $split($option, '|');

            foreach ($rules as $rule) {
                $params = [];
                if (strpos($rule, ':')) {
                    [$rule_name, $param_str] = $split($rule, ':');
                    $params = $split($param_str, ',');
                } else {
                    $rule_name = trim($rule);
                }
                $method_name = 'is_' . $rule_name;

                if (method_exists($this, $method_name) && !$this->$method_name($field, ...$params)) {
                    $this->errors[$field] = sprintf(
                        $this->errorMessages()[$field][$rule_name] ?? $validation_errors[$rule_name] ?? 'The %s is not a valid!',
                        $field,
                        ...$params
                    );
                }
            }
        }

        return empty($this->errors);
    }

    public function loadData(array $data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    public function hasError(string $field): bool
    {
        return $this->errors->has($field);
    }

    public function getError(string $field): ?string
    {
        return $this->errors->get($field);
    }

    public function getErrors(): Collection
    {
        return $this->errors;
    }

    public function getErrorsToArray(): array
    {
        return $this->errors->getAll();
    }
}
