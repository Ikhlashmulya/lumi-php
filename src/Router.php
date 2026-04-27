<?php

namespace Lumi\LumiPHP;

class Router
{
    /** @var Route[] $routes */
    private array $routes;

    public function __construct()
    {
        $this->routes = array();
    }

    public function add(string $method, string $path, callable ...$handlers): void
    {
        $route = new Route;
        $route->method = $method;
        $route->path = $path;
        $route->handlers = $handlers;

        $this->routes[] = $route;
    }

    public function has(string $method, string $uri): array
    {
        foreach ($this->routes as $route) {
            if($route->method == $method){
                $matches = PathUtil::isMatch($route->path, $uri);
                if ($matches !== null) {
                    return [$route->path, $matches, $route->handlers];
                }
            }
        }
        return [null, null, null];
    }

}