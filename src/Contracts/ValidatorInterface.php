<?php
namespace MA\PHPQUICK\Contracts;

use MA\PHPQUICK\Collection;

interface ValidatorInterface
{
    public function rules(): array;
    public function errorMessages(): array;
    public function validate(): array;
    public function loadData(array $data);
    public function hasError(string $field): bool;
    public function getError(string $field): ?string;
    public function getErrors(): Collection;
    public function getErrorsToArray(): array;
}