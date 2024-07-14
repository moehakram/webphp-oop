<?php
namespace MA\PHPQUICK\Session;

use MA\PHPQUICK\Collection;

class Session extends Collection
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        session_start();
        parent::__construct($_SESSION);

        $flashMessages = $this->get(self::FLASH_KEY, []);
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $this->set(self::FLASH_KEY, $flashMessages);
    }

    public function setFlash($key, $message)
    {
        $flashMessages = $this->get(self::FLASH_KEY, []);
        $flashMessages[$key] = [
            'remove' => false,
            'value' => $message
        ];
        $this->set(self::FLASH_KEY, $flashMessages);
    }

    public function getFlash($key)
    {
        $flashMessages = $this->get(self::FLASH_KEY, []);
        return $flashMessages[$key]['value'] ?? false;
    }

    public function set($key, $value)
    {
        parent::set($key, $value);
        $this->syncSession();
    }

    public function remove($key)
    {
        parent::remove($key);
        $this->syncSession();
    }

    public function __destruct()
    {
        $this->removeFlashMessages();
    }

    private function removeFlashMessages()
    {
        $flashMessages = $this->get(self::FLASH_KEY, []);
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $this->set(self::FLASH_KEY, $flashMessages);
    }

    private function syncSession()
    {
        $_SESSION = $this->getAll();
    }
}
