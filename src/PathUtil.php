<?php

namespace Lumi\LumiPHP;

class PathUtil
{
    public static function getParamNames(string $rawPath): array
    {
        preg_match_all('/\{([a-zA-Z0-9]+)\}/', $rawPath, $paramNames);
        return $paramNames[1];
    }

    public static function toRegexPath(string $rawPath): array|string|null
    {
        return preg_replace('/\{([a-zA-Z0-9]+)\}/', '([a-zA-Z0-9]+)', $rawPath);
    }

    public static function isMatch(string $rawPath, string $uri): array|null
    {
        $regexPath = self::toRegexPath($rawPath);
        if(!preg_match("#^$regexPath$#", $uri, $matches)){
            return null;
        }
        array_shift($matches);
        return $matches;
    }

    public static function isPrefixMatch(string $rawPath, string $uri): bool
    {
        if ($rawPath === '/') return true;

        return $uri === $rawPath || str_starts_with($uri, rtrim($rawPath, '/') . '/');
    }
}