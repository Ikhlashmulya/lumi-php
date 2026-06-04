<?php

namespace Lumi\LumiPHP\Routing;

use Lumi\LumiPHP\Helper\PathUtil;

class Router
{
    /** @var Route[] $routes */
    private array $routes;
    /** @var Route[] $routes */
    private array $middlewareRoutes;

    public function __construct()
    {
        $this->routes = array();
        $this->middlewareRoutes = array();
    }

    public function add(string $method, string $path, callable ...$handlers): void
    {
        $route = new Route;
        $route->method = $method;
        $route->path = $path;
        $route->handlers = $handlers;

        $this->routes[] = $route;
    }

    public function addMiddleware(string $path, callable ...$handlers): void
    {
        $route = new Route;
        $route->method = '*';
        $route->path = $path;
        $route->handlers = $handlers;

        $this->middlewareRoutes[] = $route;

    }

    public function match(string $method, string $uri): array
    {
        $middlewareHandlers = array();
        foreach ($this->middlewareRoutes as $route) {
            if (PathUtil::isPrefixMatch($route->path, $uri)) {
                array_push($middlewareHandlers, ...$route->handlers);
            }
        }

        foreach ($this->routes as $route) {
            if($route->method == $method){
                $matches = PathUtil::isMatch($route->path, $uri);
                if ($matches !== null) {
                    return [$route->path, $matches, array_merge($middlewareHandlers, $route->handlers)];
                }
            }
        }
        return [null, null, null];
    }
}
