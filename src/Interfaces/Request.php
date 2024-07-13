<?php

namespace MA\PHPQUICK\Interfaces;

use MA\PHPQUICK\Session\Session;

interface Request
{
    public function get(string $key = '');

    public function post(string $key = '');

    public function getContent(string $key = '');

    public function getPath(): string;

    public function getMethod(): string;

    public function cookie(string $key = '');

    public function files(string $key = '');

    public function header(string $key = ''): ?string;

    public function isMethod(string $method): bool;

    public function getClientIp(): ?string;

    public function isAjax(): bool;

    public function getUserAgent(): ?string;

    public function getQueryString(): string;

    public function user(): ?UserAuth;

    public function login(?UserAuth $user);

    public function session(): Session;
}
