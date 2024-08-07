<?php

namespace App\Domain;

use MA\PHPQUICK\Interfaces\Authenticable;

class User implements Authenticable
{
    public $id;
    public $name;
    public $username;
    public $email;
    public $password;
    public $role;
    public $is_active;
    public $activated_at;

    // private $rememberToken;

    public function getAuthIdentifier()
    {
        return $this->username;
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function getAuthIdentifierName(): string
    {
        return 'username';
    }

    public function getRememberToken(): ?string
    {
        // return $this->rememberToken;
        return null;
    }

    public function setRememberToken(string $value): void
    {
        // $this->rememberToken = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}
