<?php
namespace MA\PHPQUICK\Validation;

trait MethodsValidation {
    
    /**
     * Return true if a string is not empty
     * @param string $field
     * @return bool
     */
    private function is_required(string $field): bool
    {
        return $this->has($field) && trim($this->get($field)) !== '';
    }

    /**
     * Return true if the value is a valid email
     * @param string $field
     * @return bool
     */
    private function is_email(string $field): bool
    {
        if (!$this->has($field)) {
            return true;
        }

        return filter_var($this->get($field), FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Return true if a string has at least min length
     * @param string $field
     * @param int $min
     * @return bool
     */
    private function is_min(string $field, int $min): bool
    {
        if (!$this->has($field)) {
            return true;
        }

        return mb_strlen($this->get($field)) >= $min;
    }

    /**
     * Return true if a string cannot exceed max length
     * @param string $field
     * @param int $max
     * @return bool
     */
    private function is_max(string $field, int $max): bool
    {
        if (!$this->has($field)) {
            return true;
        }

        return mb_strlen($this->get($field)) <= $max;
    }

    /**
     * @param string $field
     * @param int $min
     * @param int $max
     * @return bool
     */
    private function is_between(string $field, int $min, int $max): bool
    {
        if (!$this->has($field)) {
            return true;
        }

        $len = mb_strlen($this->get($field));
        return $len >= $min && $len <= $max;
    }

    /**
     * Return true if a string equals the other
     * @param string $field
     * @param string $other
     * @return bool
     */
    private function is_same(string $field, string $other): bool
    {
        if ($this->has($field) && $this->has($other)) {
            return $this->get($field) === $this->get($other);
        }
        
        if (!$this->has($field) && !$this->has($other)) {
            return true;
        }

        return false;
    }


    /**
     * Return true if a string is alphanumeric
     * @param string $field
     * @return bool
     */
    private function is_alphanumeric(string $field): bool
    {
        if (!$this->has($field)) {
            return true;
        }

        return ctype_alnum($this->get($field));
    }

    /**
     * Return true if a password is secure
     * @param string $field
     * @return bool
     */
    private function is_secure(string $field): bool
    {
        if (!$this->has($field)) {
            return false;
        }

        $pattern = "#.*^(?=.{8,64})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$#";
        return preg_match($pattern, $this->get($field)) === 1;
    }

    /**
     * Return true if the $value is unique in the column of a table
     * @param string $field
     * @param string $table
     * @param string $column
     * @return bool
     */
    private function is_unique(string $field, string $table, string $column): bool
    {
        if (!$this->has($field)) {
            return true;
        }

        $sql = "SELECT $column FROM $table WHERE $column = :value";

        $stmt = \MA\PHPQUICK\Database\Database::getConnection()->prepare($sql);
        $stmt->bindValue(":value", $this->get($field));

        $stmt->execute();

        return $stmt->fetchColumn() === false;
    }

    /**
     * Return true if a value is numeric
     * @param string $field
     * @return bool
     */

    private function is_numeric(string $field): bool
    {
        if (!$this->has($field)) {
            return true;
        }

        return is_numeric($this->get($field));
    }

    /**
     * Filter sanitize
     */
    private function is_fs(string $field, string $fieldType): bool
    {
        if (!$this->has($field)) {
            return true;
        }

        $filters = [
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
    
        $filter = $filters[$fieldType] ?? FILTER_SANITIZE_SPECIAL_CHARS;
    
        $value = $this->get($field);
    
        if (is_array($filter)) {
            $result = filter_var($value, $filter['filter'], $filter['flags']);
        } else {
            $result = filter_var($value, $filter);
        }

        if ($result !== false) {
            $result = is_array($result) ? array_map('trim', $result) : trim($result);
            $this->set($field, $result);
        }
    
        return $result !== false;
    }
}