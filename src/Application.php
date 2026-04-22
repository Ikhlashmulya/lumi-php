<?php

namespace Lumi\LumiPHP;

class Application {
    private Router $router;

    public function __construct() {
        $this->router = new Router;
    }

    public function get(string $path, callable $handler) {
        $this->router->add('GET', $path, $handler);
    }

    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];

        [$path, $matches, $handler] = $this->router->has($method, $uri);
        if ($handler !== null) {
            $req = new Request($path, $matches);
            $res = new Response();
            $ctx = new Context($req, $res);
            $handler($ctx);
        }
    }
}