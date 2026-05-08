<?php

namespace Lumi\LumiPHP;

class Context 
{
    public Request $req;
    public Response $res;
    private array $context = array();
    private int $idxHandler = 0;
    private array $calledNext = array();
    private array $handlers = array();

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
        $currentIdx = $this->idxHandler;

        if (isset($this->calledNext[$currentIdx])) {
            throw new \RuntimeException('next() called multiple times');
        }

        $this->calledNext[$currentIdx] = true;

        $this->idxHandler = $currentIdx + 1;

        try {
            if (isset($this->handlers[$this->idxHandler])) {
                ($this->handlers[$this->idxHandler])($this);
            }
        } finally {
            $this->idxHandler = $currentIdx;
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
