<?php
namespace MA\PHPQUICK\Interfaces;

use MA\PHPQUICK\Http\Responses\RedirectResponse;
use MA\PHPQUICK\Http\Responses\ResponseHeaders;

interface Response
{
    public function setNoCache() : self;

    public function setNotFound($message = null);
    
    public function setForbidden();

    public function redirect(string $url): RedirectResponse;

    public function getContent() : string;

    public function headers() : ResponseHeaders;

    public function getHttpVersion() : string;

    public function getStatusCode() : int;

    public function send();

    public function setContent($content) : self;

    public function setExpiration(\DateTime $expiration) : self;

    public function setHttpVersion(string $httpVersion) : self;

    public function setStatusCode(int $statusCode, string $statusText = null) : self;
}