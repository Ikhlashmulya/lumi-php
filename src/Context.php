<?php

namespace Lumi\LumiPHP;

class Context {
    public Request $req;
    public Response $res;
    private array $context;

    public function __construct()
    {
        $this->req = new Request();
        $this->res = new Response();
    }

    public function set(string $key, mixed $value) {
        $this->context[$key] = $value;
    }

    public function get(string $key) {
        return $this->context[$key];
    }
}