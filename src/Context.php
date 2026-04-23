<?php

namespace Lumi\LumiPHP;

class Context 
{
    public Request $req;
    public Response $res;
    private array $context;

    public function __construct(Request $req, Response $res)
    {
        $this->req = $req;
        $this->res = $res;
    }

    public function set(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
    }

    public function get(string $key): mixed
    {
        return $this->context[$key];
    }
}