<?php

namespace MA\PHPQUICK\MVC;

use MA\PHPQUICK\Validator;

abstract class Model extends Validator
{
    abstract public function rules(): array;
}