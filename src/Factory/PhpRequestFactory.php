<?php

namespace Lumi\LumiPHP\Factory;

use Lumi\LumiPHP\Request;

class PhpRequestFactory
{
    public static function fromGlobals(string $method, string $path, string $uri, array $matches): Request
    {
        if ($method === 'GET') {
            return new Request(
                path: $path,
                uri: $uri,
                method: $method,
                queries: $_GET,
                headers: getallheaders(),
                matches: $matches
            );
        }

        $parseBody = [];
        if ($method === 'POST') {
            $parsedBody = $_POST;
        } else {
            [$parsedBody, $_] = request_parse_body();
            $parseBody = $parsedBody;
        }

        return new Request(
            path: $path,
            uri: $uri,
            method: $method,
            queries: $_GET,
            headers: getallheaders(),
            rawBody: file_get_contents('php://input'),
            parseBody: $parseBody,
            matches: $matches
        );
    }
}