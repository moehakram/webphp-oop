<?php

namespace App\Service;

use App\Service\UserService;
use App\Service\SessionService;
use MA\PHPQUICK\Database\Database;
use App\Repository\UserRepository;
use App\Repository\SessionRepository;

trait ServiceTrait {

    protected UserService $userService;
    protected SessionService $sessionService;

    protected function authService()
    {
        $connection = Database::getConnection();
        $userRepository = new UserRepository($connection);
        $this->userService = new UserService($userRepository);
        $sessionRepository = new SessionRepository($connection);
        $this->sessionService = new SessionService($sessionRepository);
    }
}