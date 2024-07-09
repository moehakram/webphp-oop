<?php

namespace App\Domain;

use MA\PHPQUICK\Interfaces\UserAuth;

class User implements UserAuth
{
    public string $id;
    public string $name;
    public string $password;
    public int $role;

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
