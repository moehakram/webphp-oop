<?php

namespace MA\PHPQUICK\Interfaces;

interface UserAuth 
{
    public function getId();
    public function getName();
    public function getRole();
}