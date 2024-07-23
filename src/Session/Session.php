<?php
namespace MA\PHPQUICK\Session;

use MA\PHPQUICK\Collection;

class Session extends Collection
{
    const FLASH = 'FLASH_MESSAGES';
    // Flash message types
    const FLASH_ERROR = 'error';
    const FLASH_WARNING = 'warning';
    const FLASH_INFO = 'info';
    const FLASH_SUCCESS = 'success';

    public function __construct()
    {
        session_start();
        parent::__construct($_SESSION);
        $flashMessages = $this->get(self::FLASH, []);
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['is_remove'] = true;
        }
        $this->set(self::FLASH, $flashMessages);
    }

    public function setFlash(string $name, string $message, string $type = self::FLASH_SUCCESS)
    {
        $flashMessages = $this->get(self::FLASH, []);
        $flashMessages[$name] = [
            'is_remove' => false,
            'message' => $message,
            'type' => $type
        ];
        $this->set(self::FLASH, $flashMessages);
    }

    public function getFlash(string $key)
    {
        $flashMessages = $this->get(self::FLASH, []);
        return $flashMessages[$key]['message'] ?? false;
    }

    private function removeFlashMessages()
    {
        $flashMessages = $this->get(self::FLASH);
        foreach ($flashMessages ?? [] as $key => $flashMessage) {
            if ($flashMessage['is_remove']) {
                unset($flashMessages[$key]);
            }
        }
        $this->set(self::FLASH, $flashMessages);
    }

    public function __destruct()
    {
        $this->removeFlashMessages();
        $_SESSION = $this->getAll();
    }
}
