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
            fileResolver: $fileResolver
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
