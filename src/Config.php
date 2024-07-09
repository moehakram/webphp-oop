<?php

namespace MA\PHPQUICK;

class Config
{
    protected static $config;

    protected static function loadConfig()
    {
        if (!isset(self::$config)) {
            self::$config = Application::$app->config;
        }
    }
    
    public static function get($key, $default = null)
    {
        self::loadConfig();

        $config = self::$config;
        $keys = explode('.', $key);

        foreach ($keys as $part) {
            if (isset($config[$part])) {
                $config = $config[$part];
            } else {
                return $default;
            }
        }

        return $config;
    }
}