<?php

namespace  App\Service;

use App\Domain\{Session, User};
use App\Helper\Session as HelperSession;
use App\Repository\SessionRepository;

class SessionService
{
    private SessionRepository $sessionRepository;
    protected HelperSession $session;

    public function __construct(SessionRepository $sessionRepository)
    {
        $this->sessionRepository = $sessionRepository;
        $this->session = new HelperSession();
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
        $this->session->set('exp', time()+ HelperSession::EXPIRY);
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
