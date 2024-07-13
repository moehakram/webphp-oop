<?php
namespace MA\PHPQUICK\Http\Responses;

class RedirectResponse extends Response
{
    protected $targetUrl = '';

    public function __construct(string $targetUrl, int $statusCode = ResponseHeaders::HTTP_FOUND, array $headers = [])
    {
        parent::__construct('', $statusCode, $headers);

        $this->setTargetUrl($targetUrl);
    }

    public function getTargetUrl() : string
    {
        return $this->targetUrl;
    }

    public function setTargetUrl(string $targetUrl)
    {
        $this->targetUrl = $targetUrl;
        $this->headers->set('Location', $this->targetUrl);
    }
}