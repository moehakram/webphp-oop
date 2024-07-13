<?php
namespace MA\PHPQUICK\Exception;

class HttpException extends \Exception
{
    protected $code;
    protected $message;
    protected $headers;

    public function __construct(int $statusCode = 400, string $message = null, array $headers = [], \Exception $previous = null)
    {
        $this->code = $statusCode;
        $this->headers = $headers;
        $this->message = $message ?: $this->getDefaultMessage($statusCode);
        parent::__construct($this->message, $statusCode, $previous);
    }

    protected function getDefaultMessage($statusCode)
    {
        $messages = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
        ];

        return $messages[$statusCode] ?? 'Unknown Error';
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
