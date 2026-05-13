<?php

namespace Lumi\LumiPHP;

use Lumi\LumiPHP\Emitter\PhpResponseEmitter;
use Lumi\LumiPHP\Factory\PhpRequestFactory;
use Lumi\LumiPHP\Http\Response;
use Lumi\LumiPHP\Http\Context;
use Lumi\LumiPHP\Http\Request;
use Lumi\LumiPHP\Routing\Router;
use Lumi\LumiPHP\Routing\RouterGroup;
use Lumi\LumiPHP\Routing\RouterInterface;

class Application implements RouterInterface
{
    private Router $router;
    private mixed $notFoundHandler = null;
    private mixed $onErrorHandler = null;
    private string $viewPath = '';

    public function __construct() 
    {
        $this->router = new Router;
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
        $this->viewPath = $path;
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

    public function onError(callable $handler): void
    {
        $this->onErrorHandler = $handler;
    }

    public function group(string $path, callable ...$handlers): RouterGroup
    {
        return new RouterGroup($this, $path, ...$handlers);
    }

    private function createResponse(): Response
    {
        $res = new Response();
        $res->setView($this->viewPath);
        return $res;
    }

    public function run(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        $req = PhpRequestFactory::create($method, $uri);
        $res = $this->handle($req);
        PhpResponseEmitter::emit($res);
    }

    public function handle(Request $req): Response
    {
        $res = $this->createResponse();

        [$path, $matches, $handlers] = $this->router->match($req->method, $req->uri);
        if (is_array($handlers) && count($handlers) > 0) {
            $ctx = new Context($req->withRoute($path, $matches), $res);
            $ctx->setHandlers(0, $handlers);
            try {
                $handlers[0]($ctx);
            } catch (\Throwable $e) {
                if (is_callable($this->onErrorHandler)) {
                    ($this->onErrorHandler)($e, $ctx);
                } else {
                    $res->status(500)->text('Internal Server Error');
                }
            } finally {
                return $res;
            }
        } else {
            $ctx = new Context($req, $res->status(404));
            if (is_callable($this->notFoundHandler)) {
                ($this->notFoundHandler)($ctx);
            } else {
                $res->text('Url Not Found');
            }
            return $res;
        }
    }
}
