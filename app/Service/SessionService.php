<?php

namespace App\Service;

use App\Domain\User;
use App\Domain\Session;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use MA\PHPQUICK\Collection;
use MA\PHPQUICK\Session\CookieSession;
use MA\PHPQUICK\Session\Session as SessionSession;
use MA\PHPQUICK\Traits\Token;

class SessionService
{
    use Token;

    protected const COOKIE_NAME = 'PHPQuick-MVC';
    protected const JWT_SECRET = 'fe1ed383b50832081d04bef6067540e54c66066a83cc1cf994af07';
    protected const EXPIRY_REFRESH_TOKEN = 3600 * 24 * 30 * 1; // 1 bulan
    protected const EXPIRY_ACCESS_TOKEN = 3600 * 1; // 1 jam

    private SessionRepository $sessionRepository;
    private CookieSession $sessionCookie;
    private SessionSession $session;

    public function __construct(SessionRepository $sessionRepository)
    {
        self::$secretToken = 'fe1ed383b50832081d04bef6067540e54c66066a83cc1cf994af07skajjalooldloaw';
        $this->sessionRepository = $sessionRepository;
        $this->sessionCookie = new CookieSession(self::COOKIE_NAME, self::JWT_SECRET, self::EXPIRY_REFRESH_TOKEN);
        $this->session = session();
    }

   public function create(User $user): Session
    {
        $session = new Session();
        $session->user_id = $user->id;
        $this->sessionRepository->save($session);
        
        $this->setSession($user);

        if ((bool)request()->get('remember_me')) {
            $this->setRefreshToken($user, $session->id);
        }

        return $session;
    }

    public function destroy(): void
    {
        $sessionId = $this->sessionCookie->get('id');
        if ($sessionId) {
            $this->sessionRepository->deleteById($sessionId);
            // $this->sessionRepository->deleteAll();
        }

        $this->session->set('login', null);
        $this->sessionCookie->clear();
    }

    public function current(): ?User
    {
        if ($this->verifyToken($this->session->get('login', ''), $session = new Collection())) {
            if ($this->isSessionExpired($session->get('exp'))) {
                $this->session->set('login', null);
            } else {
                return $this->mapSessionToUser($session);
            }
        }

        return $this->createUserFromRefreshToken();
    }

    private function createUserFromRefreshToken(): ?User
    {
        $userId = $this->createRefreshToken();
        if (!$userId) {
            return null;
        }

        $user = $this->mapSessionToUser($this->sessionCookie);

        session_regenerate_id(true);
        $this->setSession($user);

        return $user;
    }

    public function createRefreshToken()
    {
        if ($this->isSessionExpired($this->sessionCookie->get('exp'))) {
            return null;
        }

        if (!$sessionId = $this->sessionCookie->get('id')) {
            return null;
        }

        return $this->verifySessionInDB($sessionId);
    }

    private function setSession(User $user): void
    {
        $accessToken = $this->generateToken([
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
            'exp' => time() + self::EXPIRY_ACCESS_TOKEN
        ]);
        $this->session->add('login', $accessToken);
    }

    private function setRefreshToken(User $user, string $sessionId): void // cookie
    {
        $this->sessionCookie->add([
            'id' => $sessionId,
            'name' => $user->name,
            'role' => $user->role,
            'exp' => time() + self::EXPIRY_REFRESH_TOKEN
        ]);
        $this->sessionCookie->push();
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

    private function mapSessionToUser(Collection $session): User
    {
        $user = new User();
        $user->id = $session->get('id');
        $user->name = $session->get('name');
        $user->role = $session->get('role');
        return $user;
    }
}
