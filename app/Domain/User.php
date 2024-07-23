<?php

namespace App\Domain;

use MA\PHPQUICK\Interfaces\UserAuth;

class User implements UserAuth
{
    public $id;
    public $name;
    public $username;
    public $email;
    public $password;
    public $role;
    public $is_active;
    public $activated_at;

    public function getId(){
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getRole(){
        return $this->role;
    }

}
