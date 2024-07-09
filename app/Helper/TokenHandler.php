<?php

namespace App\Helper;
use Exception;
use Firebase\JWT\{JWT, Key};

class TokenHandler{

    private const ALGORITHM = 'HS256';

    public static function generateToken(array $payload, string $key): string
    {
        return JWT::encode($payload, $key, self::ALGORITHM );
    }

    public static function verifyToken(string $encPayload, string $key): ? \stdClass
    {
        try {
            return JWT::decode($encPayload, new Key($key, self::ALGORITHM));
        } catch (Exception $e) {
            return null;
        }
    }
}