<?php
namespace MA\PHPQUICK\Validation; // @link https://www.phptutorial.net/php-tutorial/php-validation/

use MA\PHPQUICK\Collection;

class Validation
{
    use MethodsValidation;
    
    protected const DEFAULT_ERROR_MESSAGES = [
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

    protected ErrorsValidation $errors;
    protected array $rules = [];
    protected Collection $data;

    public function __construct(array $data = [])
    {
        $this->data = new Collection([]);
        $this->errors = new ErrorsValidation([]);
        $this->loadData($data);
    }

    protected function rules(): array
    {
        return [];
    }

    protected function errorMessages(): array
    {
       return [];
    }

    public function validate($validationRules = []) : Collection
    {
        $split = fn($str, $separator) => array_map('trim', explode($separator, strtolower($str)));

        $ruleMessages = array_filter($this->errorMessages(), fn($message) => is_string($message));
        $validationErrors = array_merge(self::DEFAULT_ERROR_MESSAGES, $ruleMessages);

        foreach ($validationRules ?: $this->rules() as $field => $rules) {
            foreach ($split($rules, '|') as $rule) {
                $params = [];
                if (strpos($rule, ':')) {
                    [$ruleName, $paramStr] = $split($rule, ':');
                    $params = $split($paramStr, ',');
                } else {
                    $ruleName = trim($rule);
                }
                $methodName = 'is_' . $ruleName;

                if (method_exists($this, $methodName) && !$this->$methodName($field, ...$params)) {
                    $message = $this->errorMessages()[$field][$ruleName] ?? $validationErrors[$ruleName] ?? 'The %s is not valid!';
                    $this->errors[$field] = sprintf($message, $field, ...$params);
                }
            }
        }

        return $this->data;
    }

    protected function loadData(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function get(string $key, $default = null)
    {
        return $this->data->get($key, $default);
    }

    public function set(string $key, $value)
    {
        $this->data->set($key, $value);
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
}
