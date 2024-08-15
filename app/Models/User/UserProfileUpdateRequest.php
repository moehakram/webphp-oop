<?php

namespace App\Models\User;

use MA\PHPQUICK\MVC\Model;
use MA\PHPQUICK\Traits\PropertyAccessor;

class UserProfileUpdateRequest extends Model
{
    use PropertyAccessor;
    
    public ?string $id = null;
    public ?string $name = null;
    public ?string $username = null;
    public ?string $email = null;


    public function rules(): array
    {
        return [
            'id' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users,username',
            'email' => 'required|unique:users,email',
        ];
    }
}
