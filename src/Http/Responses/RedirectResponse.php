<?php
namespace MA\PHPQUICK\Http\Responses;

use MA\PHPQUICK\Session\Session;

class RedirectResponse extends Response
{

    public function __construct(string $targetUrl, int $statusCode = 302, array $headers = [])
    {
        if (empty($targetUrl)) {
            throw new \InvalidArgumentException('Invalid URL provided for redirect.');
        }

        parent::__construct('', $statusCode, $headers);

        $this->headers->set('Location', str_replace(['&amp;', '\n', '\r'], ['&', '', ''], $targetUrl));
    }

    public function with(array $items): self
    {
        foreach ($items as $key => $value) {
            session()->set($key, $value);
        }
        return $this;
    }

    public function withMessage(string $message, string $type = Session::FLASH_SUCCESS): self
    {
        session()->setFlash(strRandom(), $message, $type);
        return $this;
    }
}