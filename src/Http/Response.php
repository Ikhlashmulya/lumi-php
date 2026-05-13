<?php

namespace Lumi\LumiPHP\Http;

class Response 
{
    private string $viewPath = '';
    public int $statusCode = 200;
    public array $headers = [];
    public string|false $body = '';
    public string|false $redirectUrl = false;

    public function setView(string $path): void
    {
        $this->viewPath = $path;
    }

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function redirect(string $url): void
    {
        $this->statusCode = 302;
        $this->redirectUrl = $url;
    }

    public function view(string $viewName, array $data = array()): void
    {
        if ($this->viewPath === '') {
            throw new \RuntimeException('View path is not set');
        }

        $_ = $data;
        unset($data);

        $this->header('Content-Type', 'text/html; charset=utf-8');

        ob_start();
        require $this->viewPath . '/' . $viewName . '.php';
        $this->body = ob_get_clean();
    }

    public function header(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    public function text(string $text): void
    {
        $this->header('Content-Type', 'text/plain; charset=utf-8');
        $this->body = $text;
    }

    public function json(array $data): void
    {
        $this->header('Content-Type', 'application/json; charset=utf-8');
        $this->body = json_encode($data);
    }
}
