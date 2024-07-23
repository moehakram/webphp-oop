<?php

namespace App\Models\User;

use MA\PHPQUICK\MVC\Model;

class UserRegisterRequest extends Model
{
    public $name = null;
    public $username = null;
    public $email = null;
    public $password = null;
    public $password2 = null;
    public $agree = null;

    public function rules(): array
    {
        return [
            'name' => '@string|required',
            'username' => 'required|@trim|unique:users, username',
            'email' => '@email|required|email|unique:users,email',
            'password' => 'required|secure',
            'password2' => 'required|same:password',
            'agree' => 'required'
        ];
    }

    protected function messages(): array
    {
        return [
            'agree' => [
                'required' => 'Harap menyetujui persyaratan layanan kami',
            ],
        ];
    }
}
