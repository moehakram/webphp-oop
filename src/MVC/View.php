<?php
namespace MA\PHPQUICK\MVC;

use Exception;

final class View
{
    private function __construct(
        private string $view, 
        private array $data = [], 
        private ?string $layout = null
    ) {}

    public static function __callStatic(string $name, array $arguments)
    {
        $view = str_replace('_', '/', $name);
        $data = $arguments[0] ?? [];
        $layout = $arguments[1] ?? null;

        return new self($view, $data, $layout);
    }

    public function withData($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->data[$k] = $v;
            }
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function withLayout(?string $layout)
    {
        $this->layout = $layout;
        return $this;
    }

    public function display(): string
    {
        return self::render($this->view, $this->data, $this->layout);
    }

    public static function render(string $view, array $data = [], ?string $layout = null): string
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
}
