<?php

namespace Lumi\LumiPHP\Routing;

interface RouterInterface
{
    function get(string $path, callable ...$handler): void;

    function post(string $path, callable ...$handler): void;

    function put(string $path, callable ...$handler): void;

    function patch(string $path, callable ...$handler): void;

    function delete(string $path, callable ...$handler): void;

    function trace(string $path, callable ...$handler): void;

    function options(string $path, callable ...$handler): void;

    function head(string $path, callable ...$handler): void;
}