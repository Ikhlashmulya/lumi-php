<?php

namespace Lumi\LumiPHP\Emitter;

use Lumi\LumiPHP\Response;

class PhpResponseEmitter
{
    public static function emit(Response $res): void
    {
        http_response_code($res->statusCode);

        foreach ($res->headers as $key => $value) {
            header(sprintf("%s: %s", $key, $value));
        };

        if ($res->redirectUrl !== false) {
            header("Location: " . $res->redirectUrl);
            return;
        }
        
        echo $res->body;
    }
}