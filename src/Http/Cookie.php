<?php

namespace Lumi\LumiPHP\Http;

class Cookie 
{
    public string $name;
    public string $value = '';
    public int $expires = 0;
    public string $path = '/';
    public string $domain = '';
    public bool $secure = false;
    public bool $httpOnly = true;

    public function __construct(
        string $name,
        string $value = '',
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = true,
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->expires = $expires;
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;
    }
}
