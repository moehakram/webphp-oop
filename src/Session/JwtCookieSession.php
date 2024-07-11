<?php
namespace MA\PHPQUICK\Session;

use Firebase\JWT\{JWT, Key};

class JwtCookieSession
{
    protected const ALGORITHM = 'HS256';
    protected string $cookie_name;
    protected string $jwt_secret;
    protected int $expiry;
    protected array $data = [];

    public function __construct(string $cookie_name, string $jwt_secret = 'your_jwt_secret_here', int $expiry = 3600)
    {
        $this->cookie_name = $cookie_name;
        $this->jwt_secret = $jwt_secret;
        $this->expiry = $expiry;

        $token = request()->cookie($this->cookie_name);
        if (isset($token)) {
            $this->data = $this->verifyToken($token) ?? [];
        }
    }

    private function generateToken(): string
    {
        return JWT::encode($this->data, $this->jwt_secret, self::ALGORITHM);
    }

    private function verifyToken(string $token): ?array
    {
        try {
            return (array) JWT::decode($token, new Key($this->jwt_secret, self::ALGORITHM));
        } catch (\Exception $e) {
            $this->clear();
            return null;
        }
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function get(string $key)
    {
        return $this->data[$key] ?? null;
    }

    public function remove(string $key): void
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
            $this->push();
        }
    }

    public function clear(): void
    {
        $this->data = [];
        response()->setCookie($this->cookie_name, '', 1, '/');
    }

    public function push(): void
    {
        $token = $this->generateToken();
        response()->setCookie(
            $this->cookie_name,
            $token,
            time() + $this->expiry,
            '/',
            '',
            false,
            true
        );
    }
}
