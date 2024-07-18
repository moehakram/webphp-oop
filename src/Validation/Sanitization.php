<?php
namespace MA\PHPQUICK\Validation;

class Sanitization
{

    const FILTERS = [
        'string' => FILTER_SANITIZE_SPECIAL_CHARS,
        'string[]' => [
            'filter' => FILTER_SANITIZE_SPECIAL_CHARS,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'email' => FILTER_SANITIZE_EMAIL,
        'int' => [
            'filter' => FILTER_SANITIZE_NUMBER_INT,
            'flags' => FILTER_REQUIRE_SCALAR
        ],
        'int[]' => [
            'filter' => FILTER_SANITIZE_NUMBER_INT,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'float' => [
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_FLAG_ALLOW_FRACTION
        ],
        'float[]' => [
            'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
            'flags' => FILTER_REQUIRE_ARRAY
        ],
        'url' => FILTER_SANITIZE_URL,
    ];

    const DEFAULT_FILTER = FILTER_SANITIZE_SPECIAL_CHARS;

    protected $defaultFilter;
    protected $defaultTrim = true;
    protected $customFilters = [];

    public function __construct(int $defaultFilter = self::DEFAULT_FILTER, bool $defaultTrim = true, array $customFilters = [])
    {
        $this->defaultFilter = $defaultFilter;
        $this->defaultTrim = $defaultTrim;
        $this->customFilters = $customFilters + self::FILTERS;
    }
    
    protected function trimValue($data)
    {
        if (is_string($data)) {
            return trim($data);
        }
        
        if (is_array($data)) {
            return array_map([$this, 'trimValue'], $data);
        }
    
        return $data;
    }
    
    public function sanitize(array $inputs, array $fields = [], int $default_filter = FILTER_SANITIZE_SPECIAL_CHARS, array $filters = self::FILTERS, bool $trim = true): array
    {
        if ($fields) {
            $options = array_map(fn($field) => $filters[$field], $fields);
            $data = filter_var_array($inputs, $options);
        } else {
            $data = filter_var_array($inputs, $default_filter);
        }
    
        return $trim ? $this->trimValue($data) : $data;
    }

    protected function filter(array $data, array $fields) : array
    {
        $sanitization_rules = [];
        $validation_rules  = [];

        foreach ($fields as $field=>$rules) {
            if (strpos($rules, '|')) {
                [$sanitization_rules[$field], $validation_rules[$field] ] =  explode('|', $rules, 2);
            } else {
                $sanitization_rules[$field] = $rules;
            }
        }

        $inputs = $this->sanitize($data, $sanitization_rules);
        // $errors = $this->validate($inputs, $validation_rules, $messages);
        return [$inputs, $validation_rules];
    }

}
