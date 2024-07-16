<?php
namespace MA\PHPQUICK\Validation;

abstract class ValidationMethods{
    
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
        if (!isset($this->{$field})) {
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

        return (!isset($this->{$field}) && !isset($this->{$other}));
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


    
    /**
     * Return true if a value is numeric
     * @param string $field
     * @return bool
     */

    protected function is_numeric(string $field): bool
    {
        if (!isset($this->{$field})) {
            return true;
        }

        return is_numeric($this->{$field});
    }
}