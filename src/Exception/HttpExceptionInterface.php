<?php
namespace MA\PHPQUICK\Exception;

use Throwable;

Interface HttpExceptionInterface extends Throwable{
    public function getHeaders(): array;
}