<?php

namespace Lumi\LumiPHP;

class Response 
{
    public function text(string $text): void
    {
        echo $text;
    }
}