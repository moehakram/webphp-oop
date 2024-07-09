<?php

namespace MA\PHPQUICK\MVC;

use Exception;
use MA\PHPQUICK\Application;

final class View
{
    public static function render(string $view, array $data = [], string $extends = '')
    {
        try {
            $content = self::loadView($view, $data);

            if (!empty($extends)) {
                $templateContent = self::loadView('layouts/'. $extends, $data);
                return str_replace('{{content}}', $content, $templateContent);
            }

            return $content;

        } catch (Exception $e) {
            return self::handleException($e);
        }
    }

    private static function loadView(string $__VIEW, array $__DATA = []): string
    {
        $viewFilePath = config('dir.views') . $__VIEW . '.php';
        self::checkViewFile($viewFilePath);
        extract($__DATA);

        ob_start();
        include $viewFilePath;

        return ob_get_clean();
    }

    private static function checkViewFile(string $viewFilePath): void
    {
        if (!file_exists($viewFilePath)) {
            throw new Exception("File View '" . basename($viewFilePath) . "' tidak ditemukan di [$viewFilePath]");
        }
    }

    private static function handleException(Exception $e): string
    {
        return "Terjadi kesalahan: " . $e->getMessage();
    }
}
