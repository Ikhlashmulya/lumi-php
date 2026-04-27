<?php

namespace Lumi\LumiPHP;

class Context 
{
    public Request $req;
    public Response $res;
    private array $context;
    private int $idxHandler;
    private array $handlers;

    public function __construct(Request $req, Response $res)
    {
        $this->req = $req;
        $this->res = $res;
    }

    public function setHandlers(int $idxHandler, array $handlers): void
    {
        $this->idxHandler = $idxHandler;
        $this->handlers = $handlers;
    }

    public function next(): void
    {
        $this->idxHandler++;

        if (isset($this->handlers[$this->idxHandler])) {
            ($this->handlers[$this->idxHandler])($this);
        }
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