<?php

namespace Lumi\LumiPHP;

class Application 
{
    private Router $router;
    private Response $res;
    private array $middlewareRoutes = array();
    private mixed $notFoundHandler = null;

    public function __construct() 
    {
        $this->router = new Router;
        $this->res = new Response;
    }

    public function get(string $path, callable ...$handler): void
    {
        $this->router->add('GET', $path, ...$handler);
    }

    public function post(string $path, callable ...$handler): void
    {
        $this->router->add('POST', $path, ...$handler);
    }

    public function put(string $path, callable ...$handler): void
    {
        $this->router->add('PUT', $path, ...$handler);
    }

    public function patch(string $path, callable ...$handler): void
    {
        $this->router->add('PATCH', $path, ...$handler);
    }

    public function delete(string $path, callable ...$handler): void
    {
        $this->router->add('DELETE', $path, ...$handler);
    }

    public function trace(string $path, callable ...$handler): void
    {
        $this->router->add('TRACE', $path, ...$handler);
    }

    public function options(string $path, callable ...$handler): void
    {
        $this->router->add('OPTIONS', $path, ...$handler);
    }

    public function head(string $path, callable ...$handler): void
    {
        $this->router->add('HEAD', $path, ...$handler);
    }

    public function setView(string $path): void
    {
        $this->res->setView($path);
    }

    public function use(string|callable $args1, callable ...$handlers): void
    {
        $path = '/';
        if (is_string($args1)) {
            $path = $args1;
        }

        $handlerList = [];
        
        if (is_callable($args1)) {
            $handlerList[] = $args1;
        }

        array_push($handlerList, ...$handlers);

        $this->router->addMiddleware($path, ...$handlerList);
    }

    public function notFound(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    public function run(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        [$path, $matches, $handlers] = $this->router->match($method, $uri);
        if (is_array($handlers) && count($handlers) > 0) {
            $req = new Request($path, $matches);
            $res = $this->res;
            $ctx = new Context($req, $res);
            $ctx->setHandlers(0, $handlers);
            $handlers[0]($ctx);
        } else {
            $req = new Request('', []);
            $res = $this->res->status(404);
            $ctx = new Context($req, $res);
            if (is_callable($this->notFoundHandler)) {
                ($this->notFoundHandler)($ctx);
            } else {
                $res->text('Url Not Found');
            }
        }
    }
}
