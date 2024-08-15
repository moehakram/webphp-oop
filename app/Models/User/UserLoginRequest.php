<?php

namespace App\Models\User;

use MA\PHPQUICK\MVC\Model;
use MA\PHPQUICK\Traits\PropertyAccessor;

class UserLoginRequest extends Model
{
    use PropertyAccessor;
    
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
