<?php
namespace MA\PHPQUICK\Validation;

use MA\PHPQUICK\Collection;

class ErrorsValidation extends Collection
{
    public function &offsetGet(mixed $offset): mixed
    {
        if (!isset($this->items[$offset])) {
            $this->items[$offset] = null;
        }
        return $this->items[$offset];
    }
}
