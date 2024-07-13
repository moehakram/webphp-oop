<?php
namespace MA\PHPQUICK\Interfaces;

use MA\PHPQUICK\Http\Responses\ResponseHeaders;

interface Response
{
    public function __construct($content = '', int $statusCode = 200, array $headers = []);

    public function setNoCache(): Response;
    
    public function redirect(string $url): Response;

    public function getContent() : string;

    public function getHeaders() : ResponseHeaders;

    public function getHttpVersion() : string;

    public function getStatusCode() : int;

    public function send();

    public function sendHeaders();

    public function setContent($content);

    public function setExpiration(\DateTime $expiration);

    public function setHttpVersion(string $httpVersion);

    public function setStatusCode(int $statusCode, string $statusText = null);
}