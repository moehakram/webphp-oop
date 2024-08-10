<?php
namespace MA\PHPQUICK\Exception\Http;

use MA\PHPQUICK\Exception\HttpExceptionInterface;

class HttpException extends \Exception implements HttpExceptionInterface
{
    public function __construct(int $statusCode = 400, string $message = '', private array $headers = [], \Exception $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
    }

    public function getHeaders(): array{
        return $this->headers;
    }

}
