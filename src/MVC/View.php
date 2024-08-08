<?php

namespace MA\PHPQUICK\MVC;

use Exception;

final class View
{
    public static function render(string $view, array $data = [], ?string $layout = null)
    {
        try {
            $content = self::loadView($view, $data);

            if (!empty($layout)) {
                return self::renderLayout($layout, $content, $data);
            }

            return $content;
        } catch (Exception $e) {
            return self::handleException($e);
        }
    }

    private static function loadView(string $view, array $data = []): string
    {
        $viewFilePath = self::getViewFilePath($view);
        self::ensureViewFileExists($viewFilePath);

        return self::renderFile($viewFilePath, $data);
    }

    private static function getViewFilePath(string $view): string
    {
        return rtrim(config('dir.views'), '/') . '/' . str_replace('.', '/', $view) . '.php';
    }

    private static function ensureViewFileExists(string $viewFilePath): void
    {
        if (!file_exists($viewFilePath)) {
            throw new Exception("File View '" . basename($viewFilePath) . "' tidak ditemukan di [$viewFilePath]");
        }
    }

    private static function renderFile(string $filePath, array $data): string
    {
        extract($data);
        ob_start();
        include $filePath;
        return ob_get_clean();
    }

    private static function renderLayout(string $layout, string $content, array $data): string
    {
        $layoutContent = self::loadView("layouts/$layout", $data);
        return str_replace('{{content}}', $content, $layoutContent);
    }

    private static function handleException(Exception $e): string
    {
        return "Terjadi kesalahan: " . $e->getMessage();
    }

    public static function __callStatic($name, $arguments)
    {
        $view = str_replace('_', '/', $name);
        $data = $arguments[0] ?? [];
        $layout = $arguments[1] ?? null;

        return self::render($view, $data, $layout);
    }
}
