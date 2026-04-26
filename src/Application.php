<?php

namespace Lumi\LumiPHP;

class Application 
{
    private Router $router;
    private Response $res;

    public function __construct() 
    {
        $this->router = new Router;
        $this->res = new Response;
    }

    public function get(string $path, callable $handler): void
    {
        $this->router->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->router->add('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->router->add('PUT', $path, $handler);
    }

    public function patch(string $path, callable $handler): void
    {
        $this->router->add('PATCH', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->router->add('DELETE', $path, $handler);
    }

    public function trace(string $path, callable $handler): void
    {
        $this->router->add('TRACE', $path, $handler);
    }

    public function options(string $path, callable $handler): void
    {
        $this->router->add('OPTIONS', $path, $handler);
    }

    public function head(string $path, callable $handler): void
    {
        $this->router->add('HEAD', $path, $handler);
    }

    public function setView(string $path): void
    {
        $this->res->setView($path);
    }

    public function run(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        [$path, $matches, $handler] = $this->router->has($method, $uri);
        if ($handler !== null) {
            $req = new Request($path, $matches);
            $res = $this->res;
            $ctx = new Context($req, $res);
            $handler($ctx);
        }
    }
}