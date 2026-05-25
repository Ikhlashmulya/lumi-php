<?php

namespace Lumi\LumiPHP\Helper;

class Config
{
    public static array $config = [];

    public static function set(array $config): void
    {
        self::$config = $config;
    }

    public static function get(string $key, mixed $default = ''): mixed 
    {
        return self::$config[$key] ?? $default;
    }
}