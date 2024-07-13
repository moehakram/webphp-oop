<?php
namespace MA\PHPQUICK;

class Collection implements \IteratorAggregate, \Countable, \ArrayAccess
{
    protected $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function getAll()
    {
        return $this->items;
    }

    public function get(string $key, $default = null)
    {
        return $this->items[$key] ?? $default;
    }

    public function set(string $key, $value)
    {
        $this->items[$key] = $value;
    }

    public function add(string $key, $value)
    {
        $this->items[$key] = $value;
    }

    public function exchangeArray($array) : array
    {
        $oldValues = $this->items;
        $this->items = $array;

        return $oldValues;
    }

    public function remove(string $key)
    {
        if (isset($items[$key])) {
            unset($this->items[$key]);
        }
    }

    public function has(string $key): bool
    {
        return isset($this->items[$key]);
    }

    public function clear()
    {
        $this->items = [];
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
}
