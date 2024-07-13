<?php
namespace MA\PHPQUICK\Http\Responses;

class Response
{
    protected $content = '';
    protected $headers = null;
    protected $statusCode;
    protected $statusText = 'OK';
    protected $httpVersion = '1.1';

    public function __construct($content = '', int $statusCode = 200, array $headers = [])
    {
        $this->setContent($content);
        $this->headers = new ResponseHeaders($headers);
        $this->setStatusCode($statusCode);
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

    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
        // flush();
    }

    public function sendContent()
    {
        if (!headers_sent()) {
            echo $this->content;
        }
    }

    public function sendHeaders()
    {
        if (!headers_sent()) {
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
    }

    public function setContent($content)
    {
        $this->content = $content;
    }


    public function setExpiration(\DateTime $expiration)
    {
        $this->headers->set('Expires', $expiration->format('r'));
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
            $this->statusText = (string)$statusText;
        }
    }
}