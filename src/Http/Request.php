<?php

namespace MA\PHPQUICK\Http;

use MA\PHPQUICK\Interfaces\Request as InterfacesRequest;
use App\Domain\User;
use MA\PHPQUICK\Application;

class Request implements InterfacesRequest
{
    private array $request;
    private array $query;
    private array $cookies;
    private array $files;
    private array $server;

    public function __construct()
    {
        $this->request = $_POST;
        $this->query = $_GET;
        $this->cookies = $_COOKIE;
        $this->files = $_FILES;
        $this->server = $_SERVER;
    }

    public function get(string $key = '')
    {
        if ($key !== '') return $this->getValue($this->query, $key);

        return $this->clean($this->query);
    }

    public function post(string $key = '')
    {
        if ($key !== '') return $this->getValue($this->request, $key);

        return $this->clean($this->request);
    }

    public function input(string $key = '')
    {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata, true);

        if ($key !== '') return $this->getValue($request, $key);

        return $request;
    }

    public function getPath(): string
    {
        return $this->getValue($this->server, 'PATH_INFO', '/');
    }

    public function getMethod(): string
    {
        return $this->getValue($this->server, 'REQUEST_METHOD', 'GET');
    }

    public function cookie(string $key = '')
    {
        if ($key !== '') return $this->getValue($this->cookies, $key);
        return $this->clean($this->cookies);
    }

    public function files(string $key = '')
    {
        if ($key !== '') return $this->getValue($this->files, $key);
        return $this->files;
    }

    public function header(string $key = ''): ?string
    {
        return $this->getValue(getallheaders(), $key);
    }

    public function isMethod(string $method): bool
    {
        return strtoupper($this->getMethod()) === strtoupper($method);
    }

    public function getClientIp(): ?string
    {
        return $this->server['REMOTE_ADDR'] ?? null;
    }

    public function isAjax(): bool
    {
        return !empty($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public function getUserAgent(): ?string
    {
        return $this->header('User-Agent');
    }

    public function getQueryString(): string
    {
        return $this->server['QUERY_STRING'] ?? '';
    }

    private function clean($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);
                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }

        return $data;
    }

    private function getValue(array $array, string $key, string $default = null): ?string
    {
        return isset($array[$key]) ? $this->clean($array[$key]) : $default;
    }

    public function user(): ?User
    {
        return Application::$app->user;
    }

    public function login(?User $user){
        Application::$app->user = $user;
    }
}
