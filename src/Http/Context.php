<?php

namespace Lumi\LumiPHP\Http;

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

    public function next(): mixed
    {
        $currentIdx = $this->idxHandler;

        if (isset($this->calledNext[$currentIdx])) {
            throw new \RuntimeException('next() called multiple times');
        }

        $this->calledNext[$currentIdx] = true;

        $this->idxHandler = $currentIdx + 1;

        try {
            if (isset($this->handlers[$this->idxHandler])) {
                return ($this->handlers[$this->idxHandler])($this);
            }

            return null;
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

    public function status(int $code): self
    {
        $this->res->status($code);
        return $this;
    }

    public function header(string $key, string $value): self
    {
        $this->res->header($key, $value);
        return $this;
    }

    public function text(string $text): Response
    {
        $this->res->text($text);
        return $this->res;
    }

    public function json(array $data): Response
    {
        $this->res->json($data);
        return $this->res;
    }

    public function redirect(string $url): Response
    {
        $this->res->redirect($url);
        return $this->res;
    }

    public function view(string $viewName, array $data = array()): Response
    {
        $this->res->view($viewName, $data);
        return $this->res;
    }

    public function setCookie(Cookie $cookie): void
    {
        $this->res->setCookie($cookie);
    }
}
