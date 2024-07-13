<?php
namespace MA\PHPQUICK\Interfaces;

use MA\PHPQUICK\Collection;
use MA\PHPQUICK\Http\Requests\Files;
use MA\PHPQUICK\Http\Requests\RequestHeaders;

interface Request
{
    
    public static function setTrustedHeaderName(string $name, $value);

    public static function setTrustedProxies($trustedProxies);

    public function getClientIPAddress() : string;

    public function getCookies() : Collection;

    public function getFiles() : Files;

    public function get(string $key, $default = null);

    public function post(string $key, $default = null);

    public function getQuery() : Collection;

    public function getPost() : Collection;

    public function getDelete() : Collection;
    
    public function getPut() : Collection;

    public function getPatch() : Collection;

    public function getFullUrl() : string;

    public function getHeaders() : RequestHeaders;
    
    public function getHost() : string;

    public function getInput(string $name, $default = null);

    public function getJsonBody() : array;

    public function getMethod() : string;

    public function getPassword();

    public function getPath() : string;

    public function getPort() : int;

    public function getPreviousUrl(bool $fallBackToReferer = true) : string;

    public function getRawBody() : string;

    public function getServer() : Collection;

    public function getUser();

    public function isAjax() : bool;

    public function isJson() : bool;

    public function isPath(string $path, bool $isRegex = false) : bool;

    public function isSecure() : bool;

    public function isUrl(string $url, bool $isRegex = false) : bool;

    public function setMethod(string $method = null);

    public function setPath(string $path = null);

    public function setPreviousUrl(string $previousUrl);

    public function login(?UserAuth $user);

    public function user(): ?UserAuth;
}