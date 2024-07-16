<?php

namespace MA\PHPQUICK\MVC;

use MA\PHPQUICK\Validation\Validator;

abstract class Model extends Validator
{
    abstract public function rules(): array;

    public function clean(&$data)
    {
        if (is_array($data)) {
            foreach ($data as &$value) {
                $this->clean($value);
            }
        } else {
            $data = htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    protected function is_clean(string $field): bool
    {
        if (isset($this->{$field})) {
            $this->{$field} = htmlspecialchars(stripslashes(trim($this->{$field})), ENT_QUOTES, 'UTF-8');
        }
        return true;
    }

}