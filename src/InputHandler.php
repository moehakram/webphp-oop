<?php
namespace MA\PHPQUICK;

use MA\PHPQUICK\Validation\MethodsValidation;

class InputHandler{
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

    protected $inputs;
    protected $sanitizationRule = [];
    protected $validationRules = [];
    protected $messages;
    protected $errors;

    function __construct(array $inputs, array $fields = [], array $messages = [])
    {
        $this->inputs = $inputs;
        $this->messages = $messages;
        
        foreach($fields as $field => $rules){
            $field = trim($field);
            $this->validationRules[$field] = array_map('trim', explode('|', strtolower($rules)));
            foreach($this->validationRules[$field] as $key => $rule){
                if(strpos($rule, '@') !== false){
                    $this->sanitizationRule[$field] = trim(substr($rule, 1));
                    unset($this->validationRules[$field][$key]);
                }
            }
        }
    }

    public function getSanitizationRule() : array
    {
        return $this->sanitizationRule;
    }

    public function getValidationRules() : array
    {
        return $this->validationRules;
    }

    public function filterKey() : array
    {
        return [
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
            'trim' => [
                'filter' => FILTER_CALLBACK,
                'options' => fn($value) => trim(strip_tags($value)),
            ],
            'url' => FILTER_SANITIZE_URL,
        ];
    }

    /**
     * @return data
     */
    function sanitize() : array
    {
        $inputs = [];
        if ($this->sanitizationRule) {
            $options = array_map(fn($field) => $this->filterKey()[$field], $this->sanitizationRule);
            $inputs = filter_var_array($this->inputs, $options);
        } else {
            $inputs = filter_var_array($this->inputs, FILTER_SANITIZE_SPECIAL_CHARS);
        }
    
        $this->inputs = array_merge($this->inputs, $inputs);
        return $this->trimValue($this->inputs);
    }

    private function trimValue(&$data)
    {
        if (is_array($data)) {
            array_walk($data, [$this, 'trimValue']);
        }
    
        if(is_string($data)){
            $data = trim($data);
        }
        
        return $data;
    }

    /**
     * @return errors
     */
    public function validate()
    {
        $split = fn($str, $separator) => array_map('trim', explode($separator, strtolower($str)));

        $ruleMessages = array_filter($this->messages(), fn($message) => is_string($message));
        $validationErrors = array_merge(self::DEFAULT_ERROR_MESSAGES, $ruleMessages);

        foreach ($this->validationRules ?: $this->rules() as $field => $rules) {
            $rulesArray = is_string($rules) ? $split($rules, '|') : $rules;
            foreach ($rulesArray as $rule) {
                $params = [];
                if (strpos($rule, ':')) {
                    [$ruleName, $paramStr] = $split($rule, ':');
                    $params = $split($paramStr, ',');
                } else {
                    $ruleName = trim($rule);
                }
                $methodName = 'is_' . $ruleName;

                if (method_exists($this, $methodName) && !$this->$methodName($field, ...$params)) {
                    $message = $this->messages()[$field][$ruleName] ?? $validationErrors[$ruleName] ?? 'The %s is not valid!';
                    $this->errors[$field] = sprintf($message, $field, ...$params);
                }
            }
        }

        return $this->errors;
    }

    function filter() : array 
    {
        $this->sanitize();
        $this->validate();
        return [$this->inputs, $this->errors];
    }

    function getInputs(){
        return $this->inputs;
    }


    function getErrors(){
        return $this->errors;
    }


    protected function rules(): array
    {
        return [];
    }

    protected function messages(): array
    {
       return [];
    }

    public function has(string $key): bool
    {
        return isset($this->inputs[$key]);
    }

    public function get(string $key, $default = null)
    {
        return $this->inputs[$key] ?? $default;
    }

    public function set(string $key, $value)
    {
        $this->inputs[$key] = $value;
    }
}