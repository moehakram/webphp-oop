<?php

namespace App\Models\User;

class UserRegisterRequest
{
    public ?string $id = null;
    public ?string $name = null;
    public ?string $password = null;
}
