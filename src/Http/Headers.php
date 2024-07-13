<?php

namespace MA\PHPQUICK\Http;

use MA\PHPQUICK\Collection;

/**
 * Defines the list of headers
 */
class Headers extends Collection
{
    /** @var array The list of HTTP request headers that don't begin with "HTTP_" */
    protected static $specialCaseHeaders = [
        'AUTH_TYPE' => true,
        'CONTENT_LENGTH' => true,
        'CONTENT_TYPE' => true,
        'PHP_AUTH_DIGEST' => true,
        'PHP_AUTH_PW' => true,
        'PHP_AUTH_TYPE' => true,
        'PHP_AUTH_USER' => true
    ];

    public function __construct()
    {
        parent::__construct([]);
    }

    public function add(string $name, $values, bool $shouldReplace = true)
    {
        $this->set($name, $values, $shouldReplace);
    }

    public function get(string $name, $default = null, bool $onlyReturnFirst = true)
    {
        if ($this->has($name)) {
            $value = $this->items[$this->normalizeName($name)];

            if ($onlyReturnFirst) {
                return $value[0];
            }
        } else {
            $value = $default;
        }

        return $value;
    }

    public function has(string $name) : bool
    {
        return parent::has($this->normalizeName($name));
    }

    public function remove(string $name)
    {
        parent::remove($this->normalizeName($name));
    }

    public function set(string $name, $values, bool $shouldReplace = true)
    {
        $name = $this->normalizeName($name);
        $values = (array)$values;

        if ($shouldReplace || !$this->has($name)) {
            parent::set($name, $values);
        } else {
            parent::set($name, array_merge($this->items[$name], $values));
        }
    }

    protected function normalizeName(string $name) : string
    {
        return strtr(strtolower($name), '_', '-');
    }
}