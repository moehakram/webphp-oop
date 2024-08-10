<?php

namespace App\Service;

use App\Domain\User;
use App\Domain\Session;
use App\Repository\SessionRepository;
use MA\PHPQUICK\Session\CookieSession;

class SessionService
{
    protected const COOKIE_NAME = 'PHPQuick-MVC';
    protected const JWT_SECRET = 'fe1ed383b50832081d04bef6067540e54c66066a83cc1cf994af07';
    protected const EXPIRY = 3600 * 1; // 1 hour

    private SessionRepository $sessionRepository;
    private CookieSession $session;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->session = new CookieSession(self::COOKIE_NAME, self::JWT_SECRET, self::EXPIRY);
    }

    public function create(User $user): Session
    {
        $session = new Session();
        $session->user_id = $user->id;

        $this->sessionRepository->save($session);
        $this->setSession($user, $session->id);

        return $session;
    }

    public function destroy(): void
    {
        if ($id = $this->session->get('id')) {
            $this->sessionRepository->deleteById($id);
            // $this->sessionRepository->deleteAll();
            $this->session->clear();
        }
    }

    public function current(): ?User
    {
        if ($this->isSessionExpired($this->session->get('exp'))) {
            return null;
        }

        if (!$sessionId = $this->session->get('id')) {
            return null;
        }

        if (!$userId = $this->verifySessionInDB($sessionId)) {
            return null;
        }

        return $this->createUserFromSession($userId);
    }

    private function setSession(User $user, string $sessionId): void
    {
        $this->session->add([
            'id' => $sessionId,
            'name' => $user->name,
            'role' => $user->role,
            'exp' => time() + self::EXPIRY
        ]);
        $this->session->push();
    }

    private function isSessionExpired(?int $exp): bool
    {
        return $exp === null || $exp < time();
    }

    private function verifySessionInDB($sessionId)
    {
        $session = $this->sessionRepository->findById($sessionId);

        if ($session === null) {
            $this->destroy();
            return null;
        }

        return $session->user_id;
    }

    private function createUserFromSession(string $userId): User
    {
        $user = new User();
        $user->id = $userId;
        $user->name = $this->session->get('name');
        $user->role = $this->session->get('role');
        return $user;
    }
}
