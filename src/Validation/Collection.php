<?php
namespace MA\PHPQUICK\Validation;

use MA\PHPQUICK\Collection as PHPQUICKCollection;

final class Collection extends PHPQUICKCollection
{
    public function &offsetGet(mixed $offset): mixed
    {
        if (!isset($this->items[$offset])) {
            $this->items[$offset] = null;
        }
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __unset($name)
    {
        $this->remove($name);
    }
}
