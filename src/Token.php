<?php

namespace MA\PHPQUICK;

use Firebase\JWT\{JWT, Key};

class Token extends Collection
{
    private const ALGORITHM = 'HS256';
    private string $jwtSecret;

    public function __construct(string $jwtSecret, array $data = [])
    {
        parent::__construct($data);
        $this->jwtSecret = $jwtSecret;
    }

    public function generateToken(): string
    {
        return JWT::encode($this->getAll(), $this->jwtSecret, self::ALGORITHM);
    }

    public function verifyToken(string $token): bool
    {
        try {
            $data = (array) JWT::decode($token, new Key($this->jwtSecret, self::ALGORITHM));
            $this->exchangeArray($data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
