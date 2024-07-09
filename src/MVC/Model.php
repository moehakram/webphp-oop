<?php

namespace MA\PHPQUICK\MVC;

abstract class Model 
{
    const RULE_REQUIRED = 'required';
    const RULE_EMAIL = 'email';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_MATCH = 'match';

    public array $errors = [];

    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    abstract public function rules(): array;

    public function attributes(): array
    {
        return [];
    }

    public function labels(): array
    {
        return [];
    }

    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            $rules = explode('|', $rules);
            foreach ($rules as $rule) {
                $ruleName = $rule;
                $params = [];
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $params] = explode(':', $rule, 2);
                    $params = explode(',', $params);
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addErrorByRule($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorByRule($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $params[0]) {
                    $this->addErrorByRule($attribute, self::RULE_MIN, ['min' => $params[0]]);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $params[0]) {
                    $this->addErrorByRule($attribute, self::RULE_MAX, ['max' => $params[0]]);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$params[0]}) {
                    $this->addErrorByRule($attribute, self::RULE_MATCH, ['match' => $params[0]]);
                }
            }
        }
        return empty($this->errors);
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function hasError($attribute)
    {
        return isset($this->errors[$attribute]);
    }

    public function getFirstError($attribute)
    {
        return $this->errors[$attribute][0] ?? '';
    }

    private function addErrorByRule(string $attribute, string $rule, $params = [])
    {
        $params['field'] ??= $attribute;
        $errorMessage = $this->errorMessage($rule);
        foreach ($params as $key => $value) {
            $errorMessage = str_replace("{{$key}}", $value, $errorMessage);
        }
        $this->errors[$attribute][] = $errorMessage;
    }

    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    public function errorMessages(): array
    {
        return [
            self::RULE_REQUIRED => 'This field is required',
            self::RULE_EMAIL => 'This field must be a valid email address',
            self::RULE_MIN => 'Minimum length of this field must be {min}',
            self::RULE_MAX => 'Maximum length of this field must be {max}',
            self::RULE_MATCH => 'This field must match the {match} field'
        ];
    }

    public function errorMessage($rule): string
    {
        return $this->errorMessages()[$rule] ?? '';
    }
}