<?php
namespace MA\PHPQUICK\Session;

use MA\PHPQUICK\Collection;

class Session extends Collection
{
    protected const FLASH_KEY = 'FLASH_MESSAGES';
    //type flash
    protected const FLASH_ERROR = 'error';
    protected const FLASH_WARNING = 'warning';
    protected const FLASH_INFO = 'info';
    protected const FLASH_SUCCESS = 'success';

    public function __construct()
    {
        session_start();
        parent::__construct($_SESSION);

        $flashMessages = $this->get(self::FLASH_KEY, []);
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['is_remove'] = true;
        }
        $this->set(self::FLASH_KEY, $flashMessages);
    }

    public function setFlash(string $name, string $message, string $type)
    {
        $flashMessages = $this->get(self::FLASH_KEY, []);
        $flashMessages[$name] = [
            'is_remove' => false,
            'message' => $message,
            'type' => $type
        ];
        $this->set(self::FLASH_KEY, $flashMessages);
    }

    public function getFlash(string $key)
    {
        $flashMessages = $this->get(self::FLASH_KEY, []);
        return $flashMessages[$key]['message'] ?? false;
    }

    public function set(string $key, $value)
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
            if ($flashMessage['is_remove']) {
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
