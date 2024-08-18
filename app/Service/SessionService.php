<?php

namespace App\Service;

use App\Domain\User;
use App\Domain\Session;
use MA\PHPQUICK\Collection;
use MA\PHPQUICK\Traits\Token;
use App\Repository\SessionRepository;
use App\Repository\UserRepository;
use MA\PHPQUICK\Session\CookieSession;
use MA\PHPQUICK\Session\Session as SessionSession;

class SessionService
{
    use Token;

    protected const COOKIE_NAME = 'PHPQuick-MVC';
    protected const JWT_SECRET = 'fe1ed383b50832081d04bef6067540e54c66066a83cc1cf994af07';
    protected const EXPIRY_REFRESH_TOKEN = 3600 * 24 * 30 * 1; // 1 bulan
    protected const EXPIRY_ACCESS_TOKEN = 3600 * 1; // 1 jam

    private CookieSession $sessionCookie;

    public function __construct(
        private SessionRepository $sessionRepository, 
        private UserRepository $userRepository,
        private SessionSession $session
    ){
        self::$secretToken = 'fe1ed383b50832081d04bef6067540e54c66066a83cc1cf994af07skajjalooldloaw';
        $this->sessionCookie = new CookieSession(self::COOKIE_NAME, self::JWT_SECRET, self::EXPIRY_REFRESH_TOKEN);
    }

   public function create(User $user): Session
    {
        $session = new Session();
        $session->userId = $user->id;
        $this->sessionRepository->save($session);
        
        $this->session->add('login', $this->accessToken($user));

        if ((bool)request()->get('remember_me')) {
            $this->createRefreshToken($user->username, $session->id);
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
        session_unset();
        session_destroy();
    }

    public function current(): ?User
    {
        if ($this->verifyToken($this->session->get('login', ''), $session = new Collection())) {
            if (! $this->isSessionExpired($session->get('exp'))) {
                $user = new User();
                $user->id = $session->get('id');
                $user->username = $session->get('username');
                $user->name = $session->get('name');
                $user->role = $session->get('role');
                return $user;
            }
        }
        session_regenerate_id(true);
        return $this->createAccessToken();
    }

    private function createAccessToken(): ?User
    {
        if ($this->isSessionExpired($this->sessionCookie->get('exp'))) {
            return null;
        }

        if (!$sessionId = $this->sessionCookie->get('id')) {
            return null;
        }

        $user = $this->verifySessionRefreshTokenInDB($sessionId);

        if (!$user) {
            return null;
        }
        $this->session->add('login', $this->accessToken($user));
        return $user;
    }

    private function accessToken(User $user): string
    {
        return $this->generateToken([
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'role' => $user->role,
            'exp' => time() + self::EXPIRY_ACCESS_TOKEN
        ]);
    }

    private function createRefreshToken(string $username,string $sessionId): void // cookie
    {
        $this->sessionCookie->add([
            'id' => $sessionId,
            'username' => $username,
            'sessionId' => session_id(),
            'exp' => time() + self::EXPIRY_REFRESH_TOKEN
        ]);
        $this->sessionCookie->push();
    }

    private function isSessionExpired(?int $exp): bool
    {
        return $exp === null || $exp < time();
    }

    private function verifySessionRefreshTokenInDB($sessionId): ?User
    {
        $session = $this->sessionRepository->findById($sessionId);

        if ($session === null) {
            $this->destroy();
            return null;
        }

        return $this->userRepository->findById($session->userId);
    }
}
