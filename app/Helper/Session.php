<?php
namespace App\Helper;

use Firebase\JWT\{JWT, Key};

class Session
{
    protected const COOKIE_NAME = 'PHPQuick-MVC';
    public const EXPIRY = 3600; // Waktu kedaluwarsa cookie dalam detik (1 jam)
    protected const JWT_SECRET = 'kRd9SO75b0MffA6ThNjW0lYfZpUJzwbiwN9moDf0wQvyLWmBdrnYbCZ4IekHQVNenFD8gt4sKreL7Z'; // Ganti dengan secret yang kuat
    protected const ALGORITHM = 'HS256';

    protected array $data = [];

    public function __construct()
    {
        $token = request()->cookie(self::COOKIE_NAME);
        if (isset($token)) {
            $this->data = $this->verifyToken($token) ?? [];
        }
    }

    protected function generateToken(): string
    {
        return JWT::encode($this->data, self::JWT_SECRET, self::ALGORITHM);
    }

    protected function verifyToken($token): ?array
    {
        try {
            return (array) JWT::decode($token, new Key(self::JWT_SECRET, self::ALGORITHM));
        } catch (\Exception $e) {
            $this->clear();
            return null;
        }
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    public function get($key)
    {
        return $this->data[$key] ?? null;
    }

    public function remove($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
            $this->save();
        }
    }

    public function clear()
    {
        $this->data = [];
        setcookie(self::COOKIE_NAME, '', 1, '/');
    }

    public function save()
    {
        $token = $this->generateToken();
        setcookie(
            self::COOKIE_NAME,
            $token,
            time() + self::EXPIRY,
            '/',
            '',
            false,
            true
        );
    }
}
