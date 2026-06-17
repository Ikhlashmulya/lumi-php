<?php

namespace Lumi\LumiPHP\Factory;

use Lumi\LumiPHP\Http\Request;

class PhpRequestFactory
{
    public static function create(string $method, string $uri): Request
    {
        $method = strtoupper($method);

        $parseBody = self::parseBody($method);

        $fileResolver = fn () => UploadedFileFactory::fromGlobals();

        return new Request(
            method: $method,
            uri: $uri,
            queries: $_GET,
            headers: self::headers(),
            rawBody: self::rawBody(),
            parseBody: $parseBody,
            fileResolver: $fileResolver,
            cookies: self::cookies(),
            ip: self::getRequestIp()
        );
    }

    private static function parseBody(string $method): array
    {
        if ($method === 'GET' || $method === 'HEAD' || ! self::hasBody()) {
            return [];
        }

        if ($method === 'POST') {
            return $_POST;
        }

        if (! function_exists('request_parse_body')) {
            return [];
        }

        try {
            [$parsedBody] = request_parse_body();
        } catch (\Throwable) {
            return [];
        }

        return is_array($parsedBody) ? $parsedBody : [];
    }

    private static function hasBody(): bool
    {
        return (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0;
    }

    private static function headers(): array
    {
        return function_exists('getallheaders') ? getallheaders() : [];
    }

    private static function rawBody(): string
    {
        return file_get_contents('php://input') ?: '';
    }

    private static function cookies(): array
    {
        $cookies = [];
        foreach ($_COOKIE as $key => $value) {
            $cookies[$key] = $value;
        }
        return $cookies;
    }

    private static function getRequestIp(): string 
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ip_list[0]);
        }
        
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }
}
