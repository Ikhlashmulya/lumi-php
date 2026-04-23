<?php

namespace Lumi\LumiPHP;

class Router
{
    private array $routes;

    public function __construct()
    {
        $this->routes = array();
    }

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[$method][$path] = $handler;
    }

    public function has(string $method, string $uri): array
    {
        foreach ($this->routes[$method] as $path => $handler) {
            $matches = PathUtil::isMatch($path, $uri);
            if ($matches !== null) {
                return [$path, $matches, $handler];
            }
        }
        return [null, null, null];
    }

}