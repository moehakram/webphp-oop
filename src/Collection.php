<?php
namespace MA\PHPQUICK;

class Collection implements \IteratorAggregate, \Countable, \ArrayAccess
{
    protected array $items = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $key => $value) {
            $this->add($key, $value);
        }
    }

    public function getAll(): array
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

    public function exchangeArray(array $array): array
    {
        $oldValues = $this->items;
        $this->items = $array;

        return $oldValues;
    }

    public function remove(string $key)
    {
        unset($this->items[$key]);
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
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
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
        $this->remove($offset);
    }

    // protected function clean($data)
    // {
    //     if (is_array($data)) {
    //         $cleanedArray = [];
    //         array_walk($data, function (&$value, $key) use (&$cleanedArray) {
    //             $cleanedKey = $this->clean($key);
    //             $cleanedValue = $this->clean($value);
    //             $cleanedArray[$cleanedKey] = $cleanedValue;
    //         });
    //         return $cleanedArray;
    //     } else {
    //         return htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
    //     }
    // }
}
