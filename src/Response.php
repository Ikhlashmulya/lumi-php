<?php

namespace Lumi\LumiPHP;

class Response 
{
    public function text(string $text): void
    {
        header('Content-Type: text/plain; charset=utf-8');
        echo $text;
    }

    public function json(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
    }
}