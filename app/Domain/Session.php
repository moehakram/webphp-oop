<?php

namespace App\Domain;

class Session
{
    public int $id;
    public string $user_id;

    public function get($name){
        return 'halo ' . $name;
    }
}
