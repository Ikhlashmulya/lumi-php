<?php

namespace Lumi\LumiPHP\Factory;

use Lumi\LumiPHP\Request;

class PhpRequestFactory
{
    public static function create(string $method, string $path, string $uri, array $matches): Request
    {
        $method = strtoupper($method);

        $parseBody = self::parseBody($method);

        return new Request(
            path: $path,
            matches: $matches,
            method: $method,
            uri: $uri,
            queries: $_GET,
            headers: self::headers(),
            rawBody: self::rawBody(),
            parseBody: $parseBody
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
}
