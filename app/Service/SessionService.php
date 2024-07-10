<?php

namespace  App\Service;

use App\Domain\{Session, User};
use MA\PHPQUICK\Session\JwtCookieSession;
use App\Repository\SessionRepository;

class SessionService
{
    protected const COOKIE_NAME = 'PHPQuick-MVC'; // Ganti dengan secret yang kuat
    protected const JWT_SECRET = 'kRd9SO75b0MffA6ThNjW0lYfZpUJzwbiwN9moDf0wQvyLWmBdrnYbCZ4IekHQVNenFD8gt4sKreL7Z'; // Ganti dengan secret yang kuat
    protected const EXPIRY = 3600 * 1; // 1 jam
   
    private SessionRepository $sessionRepository;
    protected JwtCookieSession $session;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->session = new JwtCookieSession(self::COOKIE_NAME, self::JWT_SECRET, self::EXPIRY);
    }

    public function create(User $user): Session
    {
        $session = new Session();
        $session->id = strRandom(11);
        $session->userId = $user->id;
        
        $this->sessionRepository->save($session);
        $this->setSession($user, $session->id);

        return $session;
    }

    private function setSession(User $user, string $sessionId): void
    {
        $this->session->set('id', $sessionId);
        $this->session->set('name', $user->name);
        $this->session->set('role', $user->role);
        $this->session->set('exp', time() + self::EXPIRY);
        $this->session->save();
    }

    public function destroy()
    {
        if($id = $this->session->get('id')){
            $this->sessionRepository->deleteById($id);
            // $this->sessionRepository->deleteAll();
            $this->session->clear();
        }
    }

    public function current(): ?User
    {
        $exp = $this->session->get('exp');

        if ($this->isSessionExpired($exp)) {
            return null;
        };

        if(!$id = $this->verifySessionInDB()){
            return null;
        };
        
        $user = new User();
        $user->id = $id;
        $user->name = $this->session->get('name');
        $user->role = $this->session->get('role');

        return $user;
    }

    private function isSessionExpired($exp): bool
    {
        return $exp === null || $exp < time();
    }

    public function verifySessionInDB(){
        if(!$this->session->get('id')){
            return null;
        };

        $session = $this->sessionRepository->findById($this->session->get('id'));

        if ($session === null) {
            $this->destroy();
            return null;
        }

        return $session->userId;
    }
}
