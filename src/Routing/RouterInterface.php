<?php

namespace Lumi\LumiPHP\Routing;

interface RouterInterface
{
    function get(string $path, mixed ...$handler): void;

    function post(string $path, mixed ...$handler): void;

    function put(string $path, mixed ...$handler): void;

    function patch(string $path, mixed ...$handler): void;

    function delete(string $path, mixed ...$handler): void;

    function trace(string $path, mixed ...$handler): void;

    function options(string $path, mixed ...$handler): void;

    function head(string $path, mixed ...$handler): void;
}