<?php

namespace App\Models\User;

use MA\PHPQUICK\MVC\Model;

class UserLoginRequest extends Model
{
    public $username = null;
    public $password = null;
    public $remember_me = null;
    
    public function rules(): array
    {
        return [
            'username' => 'required',
            'password' => 'required'
        ];
    }
}
