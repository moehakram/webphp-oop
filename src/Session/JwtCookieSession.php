<?php
namespace MA\PHPQUICK\Session;

use Firebase\JWT\{JWT, Key};
use MA\PHPQUICK\Collection;
use MA\PHPQUICK\Http\Responses\Cookie;

class JwtCookieSession extends Collection
{
    private const ALGORITHM = 'HS256';
    private string $cookie_name;
    private string $jwt_secret;
    private int $expiration;

    public function __construct(string $cookie_name, string $jwt_secret, int $expiration = 3600)
    {
        $this->cookie_name = $cookie_name;
        $this->jwt_secret = $jwt_secret;
        $this->expiration = $expiration;

        $token = request()->getCookies()->get($this->cookie_name);
        if (isset($token)) {
            $data = $this->verifyToken($token);
            parent::__construct($data);
        }
    }

    private function generateToken(): string
    {
        return JWT::encode($this->getAll(), $this->jwt_secret, self::ALGORITHM);
    }

    private function verifyToken(string $token): array
    {
        try {
            return (array) JWT::decode($token, new Key($this->jwt_secret, self::ALGORITHM));
        } catch (\Exception $e) {
            $this->clear();
            return [];
        }
    }

    public function clear(): void
    {
        parent::clear();
        response()->getHeaders()->deleteCookie($this->cookie_name);
    }

    public function push(): void
    {
        $token = $this->generateToken();
        response()->getHeaders()->setCookie(new Cookie(
            $this->cookie_name,
            $token,
            time() + $this->expiration,
            '/',
            '',
            false,
            true
        ));
    }
}
