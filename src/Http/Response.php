<?php

namespace MA\PHPQUICK\Http;

use MA\PHPQUICK\Exception\ForbiddenException;
use MA\PHPQUICK\Exception\NotFoundException;
use MA\PHPQUICK\Interfaces\Response as InterfacesResponse;
use MA\PHPQUICK\MVC\View;
use MA\PHPQUICK\Session;

class Response implements InterfacesResponse
{
    private array $headers = [];
    private $content = '';
    private int $statusCode = 200;
    protected Session $session;

    public const STATUS_TEXTS = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getStatusText(): string
    {
        return self::STATUS_TEXTS[$this->statusCode] ?? 'unknown status';
    }

    public function setHeader(string $header): Response
    {
        $this->headers[] = $header;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setContent($content): Response
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setStatusCode(int $code): Response
    {
        if (!$this->isInvalid($code)) {
            $this->statusCode = $code;
        }
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public static function redirect(string $url): void
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('Invalid URL provided for redirect.');
        }

        header('Location: ' . str_replace(['&amp;', '\n', '\r'], ['&', '', ''], $url));
        exit;
    }

    public function setJson(array $content = []): Response
    {
        $this->setHeader('Content-Type: application/json; charset=UTF-8');
        $this->setContent(!empty($content) ? json_encode($content) : json_encode($this->content));
        return $this;
    }

    public function setPlainText(string $content = ''): Response
    {
        $this->setHeader('Content-Type: text/plain; charset=UTF-8');
        if ($content !== '') $this->setContent($content);
        return $this;
    }

    public function setContentFromFile(string $filePath): Response
    {
        if (file_exists($filePath)) {
            $this->setContent(file_get_contents($filePath));
        }
        return $this;
    }

    public function setCookie(string $name, string $value, int $expire = 0, string $path = '/', string $domain = '', bool $secure = false, bool $httponly = false): Response
    {
        $cookieString = sprintf(
            '%s=%s; expires=%s; path=%s; domain=%s; secure=%s; httponly=%s',
            $name,
            urlencode($value),
            ($expire > 0) ? gmdate('D, d M Y H:i:s T', $expire) : 0,
            $path,
            $domain,
            $secure ? 'true' : 'false',
            $httponly ? 'true' : 'false'
        );

        $this->setHeader("Set-Cookie: $cookieString");
        return $this;
    }

    public function setDownload(string $filePath, string $fileName): Response
    {
        $this->setHeader('Content-Type: application/octet-stream');
        $this->setHeader("Content-Disposition: attachment; filename=\"$fileName\"");
        $this->setContentFromFile($filePath);
        return $this;
    }

    public function setNotFound($message = null)
    {
        throw new NotFoundException(View::render('error/404', [
            'message' => $message
        ]));
    }
    
    public function setForbidden()
    {
        throw new ForbiddenException(View::render('error/403'));
    }

    public function setNoCache(): Response
    {
        $this->setHeader('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        $this->setHeader('Pragma: no-cache');
        $this->setHeader('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        return $this;
    }

    public function setCacheHeaders(int $maxAgeInSeconds = 3600, string $cacheControl = 'public'): Response
    {
        $this->setHeader('Cache-Control: ' . $cacheControl . ', max-age=' . $maxAgeInSeconds);
        $this->setHeader('Expires: ' . gmdate('D, d M Y H:i:s T', time() + $maxAgeInSeconds));
        return $this;
    }

    private function isInvalid(int $statusCode): bool
    {
        return $statusCode < 100 || $statusCode >= 600;
    }

    private function sendHeaders()
    {
        if (!headers_sent()) {
            foreach ($this->headers as $header) {
                header($header);
            }
        }
        http_response_code($this->statusCode);
    }

    private function sendContent()
    {
        echo $this->content;
    }

    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
    }
}
