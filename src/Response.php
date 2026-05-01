<?php

namespace Lumi\LumiPHP;

class Response 
{
    private string $viewPath;

    public function setView(string $path): void
    {
        $this->viewPath = $path;
    }

    public function status(int $code): self
    {
        http_response_code($code);
        return $this;
    }

    public function redirect(string $url): void
    {
        header("Location: $url", response_code: 302);
    }

    public function view(string $vw, array $data = array()): void
    {
        $_ = $data;

        $this->header('Content-Type', 'text/html; charset=utf-8');

        require_once $this->viewPath . '/' . $vw . '.php';
    }

    public function header(string $key, string $value): void
    {
        header(sprintf("%s: %s", $key, $value));
    }

    public function text(string $text): void
    {
        $this->header('Content-Type', 'text/plain; charset=utf-8');
        echo $text;
    }

    public function json(array $data): void
    {
        $this->header('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data);
    }
}