<?php
namespace MA\PHPQUICK\MVC;

use Exception;

final class View
{
    public function __construct(
        private string $view, 
        private array $data = [], 
        private ?string $layout = null
    ) {}

    public static function __callStatic(string $name, array $arguments): self
    {
        return new self(
            view: str_replace('_', '/', $name),
            data: $arguments[0] ?? [],
            layout: $arguments[1] ?? null
        );
    }

    public function withData(array|string $key, mixed $value = null): self
    {
        $this->data = is_array($key) ? array_merge($this->data, $key) : [...$this->data, $key => $value];
        return $this;
    }

    public function withLayout(string $layout): self
    {
        $this->layout = $layout;
        return $this;
    }

    public function render(): string
    {
        return $this->renderView($this->view, $this->data, $this->layout);
    }

    private function renderView(string $view, array $data = [], ?string $layout = null): string
    {
        try {
            $content = $this->loadView($view, $data);

            return $layout 
                ? $this->renderLayout($content, $data, $layout) 
                : $content;
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    private function loadView(string $view, array $data = []): string
    {
        $viewFilePath = $this->getViewFilePath($view);
        $this->ensureViewFileExists($viewFilePath);

        return $this->renderFile($viewFilePath, $data);
    }

    private function getViewFilePath(string $view): string
    {
        return rtrim(config('dir.views'), '/') . '/' . str_replace('.', '/', $view) . '.php';
    }

    private function ensureViewFileExists(string $viewFilePath): void
    {
        if (!file_exists($viewFilePath)) {
            throw new Exception("File View '" . basename($viewFilePath) . "' tidak ditemukan di [$viewFilePath]");
        }
    }

    private function renderFile(string $filePath, array $data): string
    {
        extract($data);
        ob_start();
        include $filePath;
        return ob_get_clean();
    }

    private function renderLayout(string $content, array $data, string $layout): string
    {
        $layoutContent = $this->loadView('layouts/' . $layout, $data);
        return str_replace('{{content}}', $content, $layoutContent);
    }

    private function handleException(Exception $e): string
    {
        return "Terjadi kesalahan: " . $e->getMessage();
    }
}
