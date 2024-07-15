<?php

namespace App\Models\User;

use MA\PHPQUICK\MVC\Model;

class UserRegisterRequest extends Model
{
    public ?string $id = null;
    public ?string $name = null;
    public ?string $password = null;

    public function rules(): array
    {
        return [
            'id' => 'required|unique:users, id',
            'name' => 'required',
            'password' => 'required',
        ];
    }
}
