<?php

namespace Lumi\LumiPHP;

class Response 
{
    public function header(string $key, string $value): void
    {
        header(sprintf("%s: %s", $key, $value));
    }

    public function text(string $text): void
    {
        $this->header('Content-Type', 'application/json; charset=utf-8');
        echo $text;
    }

    public function json(array $data): void
    {
        $this->header('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data);
    }
}