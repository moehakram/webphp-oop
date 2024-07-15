<?php
namespace MA\PHPQUICK; // @link https://www.phptutorial.net/php-tutorial/php-validation/

abstract class Validator
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
        // same
        'same' => 'The %s must match with %s',
        // alphanumeric
        'alphanumeric' => 'The %s should have only letters and numbers',
        //secure
        'secure' => 'The %s must have between 8 and 64 characters and contain at least one number, one upper case letter, one lower case letter and one special character',
        // unique:tabel,field
        'unique' => 'The %s already exists',
        // 'new_rule' => "Error message for the new rule"
    ];

    // protected function is_new_rule(string $field, $params) : bool{
    //     return true;
    // }

    protected Errors $errors;

    public function __construct()
    {
        $this->errors = new Errors();
    }

    abstract public function rules(): array;

    public function errorMessages(): array
    {
       return [];
    }

    /**
     * Validate
     * @return bool
     */

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
                $fn = 'is_' . $rule_name;

                if (is_callable([$this, $fn])) {
                    $pass = $this->$fn($field, ...$params);
                    if (!$pass) {
                        $this->errors[$field] = sprintf(
                            $this->errorMessages()[$field][$rule_name] ?? $validation_errors[$rule_name] ?? 'The data is not a valid !',
                            $field,
                            ...$params
                        );
                    }
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

    public function getErrors(): Errors
    {
        return $this->errors;
    }

    /**
     * Return true if a string is not empty
     * @param string $field
     * @return bool
     */
    protected function is_required(string $field): bool
    {
        return isset($this->{$field}) && trim($this->{$field}) !== '';
    }

    /**
     * Return true if the value is a valid email
     * @param string $field
     * @return bool
     */
    protected function is_email(string $field): bool
    {
        if (empty($this->{$field})) {
            return true;
        }

        return filter_var($this->{$field}, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Return true if a string has at least min length
     * @param string $field
     * @param int $min
     * @return bool
     */
    protected function is_min(string $field, int $min): bool
    {
        if (!isset($this->{$field})) {
            return true;
        }

        return mb_strlen($this->{$field}) >= $min;
    }

    /**
     * Return true if a string cannot exceed max length
     * @param string $field
     * @param int $max
     * @return bool
     */
    protected function is_max(string $field, int $max): bool
    {
        if (!isset($this->{$field})) {
            return true;
        }

        return mb_strlen($this->{$field}) <= $max;
    }

    /**
     * @param string $field
     * @param int $min
     * @param int $max
     * @return bool
     */
    protected function is_between(string $field, int $min, int $max): bool
    {
        if (!isset($this->{$field})) {
            return true;
        }

        $len = mb_strlen($this->{$field});
        return $len >= $min && $len <= $max;
    }

    /**
     * Return true if a string equals the other
     * @param string $field
     * @param string $other
     * @return bool
     */
    protected function is_same(string $field, string $other): bool
    {
        if (isset($this->{$field}, $this->{$other})) {
            return $this->{$field} === $this->{$other};
        }

        if (!isset($this->{$field}) && !isset($this->{$other})) {
            return true;
        }

        return false;
    }

    /**
     * Return true if a string is alphanumeric
     * @param string $field
     * @return bool
     */
    protected function is_alphanumeric(string $field): bool
    {
        if (!isset($this->{$field})) {
            return true;
        }

        return ctype_alnum($this->{$field});
    }

    /**
     * Return true if a password is secure
     * @param string $field
     * @return bool
     */
    protected function is_secure(string $field): bool
    {
        if (!isset($this->{$field})) {
            return false;
        }

        $pattern = "#.*^(?=.{8,64})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#";
        return preg_match($pattern, $this->{$field});
    }

    /**
     * Return true if the $value is unique in the column of a table
     * @param string $field
     * @param string $table
     * @param string $column
     * @return bool
     */
    protected function is_unique(string $field, string $table, string $column): bool
    {
        if (!isset($this->{$field})) {
            return true;
        }

        $sql = "SELECT $column FROM $table WHERE $column = :value";

        $stmt = \MA\PHPQUICK\Database\Database::getConnection()->prepare($sql);
        $stmt->bindValue(":value", $this->{$field});

        $stmt->execute();

        return $stmt->fetchColumn() === false;
    }
}
