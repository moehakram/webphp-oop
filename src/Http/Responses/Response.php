<?php
namespace MA\PHPQUICK\Http\Responses;

use MA\PHPQUICK\Exception\HttpException;
use MA\PHPQUICK\Interfaces\Response as IResponse;
use MA\PHPQUICK\MVC\View;

class Response implements IResponse
{
    protected $content;
    protected int $statusCode;
    protected string $statusText;
    protected string $httpVersion = '1.1';
    protected ResponseHeaders $headers;
    
    public function __construct($content = '', int $statusCode = 200, array $headers = [])
    {
        $this->setContent($content);
        $this->setStatusCode($statusCode);
        $this->headers = new ResponseHeaders($headers);
    }

    public function setNoCache()
    {
        $this->headers->add('Cache-Control','no-store, no-cache, must-revalidate, max-age=0');
        $this->headers->add('Pragma', 'no-cache');
        $this->headers->add('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
    }

    public function setNotFound($message = null)
    {
        $view = View::render('error/404', [
            'message' => $message
        ]);
        throw new HttpException(404, $view);
    }

    public function redirect(string $targetUrl): Response
    {
        if (empty($targetUrl)) {
            throw new \InvalidArgumentException('Invalid URL provided for redirect.');
        }

        $this->setStatusCode(302);
        $this->headers->set('Location', str_replace(['&amp;', '\n', '\r'], ['&', '', ''], $targetUrl));
        return $this;
    }

    public function getContent() : string
    {
        return $this->content;
    }

    public function getHeaders() : ResponseHeaders
    {
        return $this->headers;
    }

    public function getHttpVersion() : string
    {
        return $this->httpVersion;
    }

    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            $this->sendHeaders();
        }
        $this->sendContent();
    }

    protected function sendContent(): void
    {
        echo $this->content;
    }

    public function sendHeaders()
    { 
        header(
            sprintf(
                'HTTP/%s %s %s',
                $this->httpVersion,
                $this->statusCode,
                $this->statusText
            ),
            true,
            $this->statusCode
        );

        foreach ($this->headers->getAll() as $name => $values) {
            foreach ($values as $value) {
                header("$name:$value", false, $this->statusCode);
            }
        }

        foreach ($this->headers->getCookies(true) as $cookie) {
            $options = [
                'expires' => $cookie->getExpiration(),
                'path' => $cookie->getPath(),
                'domain' => $cookie->getDomain(),
                'secure' => $cookie->isSecure(),
                'httponly' => $cookie->isHttpOnly(),
            ];

            if (!$sameSite = $cookie->getSameSite()) {
                $options['samesite'] = $sameSite;
            }

            setcookie($cookie->getName(), $cookie->getValue(), $options);
        }
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function setHttpVersion(string $httpVersion)
    {
        $this->httpVersion = $httpVersion;
    }

    public function setStatusCode(int $statusCode, string $statusText = null)
    {
        $this->statusCode = $statusCode;

        if ($statusText === null && isset(ResponseHeaders::STATUS_TEXTS[$statusCode])) {
            $this->statusText = ResponseHeaders::STATUS_TEXTS[$statusCode];
        } else {
            $this->statusText = $statusText;
        }
    }

    
    public function setExpiration(\DateTime $expiration){
        $this->headers->set('Expires', $expiration->format('r'));
    }
}