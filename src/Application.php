<?php

namespace Lumi\LumiPHP;

use Lumi\LumiPHP\Emitter\PhpResponseEmitter;
use Lumi\LumiPHP\Factory\PhpRequestFactory;
use Lumi\LumiPHP\Helper\Config;
use Lumi\LumiPHP\Http\Response;
use Lumi\LumiPHP\Http\Context;
use Lumi\LumiPHP\Http\Request;
use Lumi\LumiPHP\Routing\Router;
use Lumi\LumiPHP\Routing\RouterGroup;
use Lumi\LumiPHP\Routing\RouterInterface;

class Application implements RouterInterface
{
    private Router $router;
    private mixed $onNotFoundHandler = null;
    private mixed $onErrorHandler = null;
    private string $viewPath = '';

    public function __construct(array $config = []) 
    {
        $this->router = new Router;
        Config::set($config);
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

    public function onNotFound(callable $handler): void
    {
        $this->onNotFoundHandler = $handler;
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
        $debugMode = Config::get('debug', false);
        $res = $this->createResponse();

        [$path, $matches, $handlers] = $this->router->match($req->method, $req->uri);
        if (!$this->hasHandlers($handlers)) {
            return $this->handleNotFound($req, $res);
        }

        $ctx = new Context($req->withRoute($path, $matches), $res);
        $ctx->setHandlers(0, $handlers);

        return $this->runHandlers($ctx, $handlers, $res);
    }

    private function hasHandlers(mixed $handlers): bool
    {
        return is_array($handlers) && count($handlers) > 0;   
    }

    private function runHandlers(Context $ctx, array $handlers, Response $res): Response
    {
        try {
            return $this->toResponse($handlers[0]($ctx), $res);
        } catch (\Throwable $e) {
            return $this->handleError($e, $ctx, $res);
        }
    }

    private function handleNotFound(Request $req, Response $res): Response
    {
        $ctx = new Context($req, $res->status(404));
        if (!is_callable($this->onNotFoundHandler)) {
            return $ctx->text('Url Not Found');
        }

        $result = ($this->onNotFoundHandler)($ctx);
        return $this->toResponse($result, $res);
    }

    private function handleError(\Throwable $e, Context $ctx, Response $res): Response
    {
        if (!is_callable($this->onErrorHandler)) {
            return $ctx->status(500)->text('Internal Server Error');
        }

        $result = ($this->onErrorHandler)($e, $ctx);
        return $this->toResponse($result, $res);
    }

    private function toResponse(mixed $result, Response $fallbackResponse): Response
    {
        return $result instanceof Response ? $result : $fallbackResponse;
    }
}
