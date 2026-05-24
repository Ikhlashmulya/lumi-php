<?php

namespace Lumi\LumiPHP\Emitter;

use Lumi\LumiPHP\Http\Response;

class PhpResponseEmitter
{
    public static function emit(Response $res): void
    {
        http_response_code($res->statusCode);

        foreach ($res->headers as $key => $value) {
            header(sprintf("%s: %s", $key, $value));
        };
    
        foreach ($res->cookies as $cookie) {
            setcookie(
                $cookie->name,
                $cookie->value,
                $cookie->expires,
                $cookie->path,
                $cookie->domain,
                $cookie->secure,
                $cookie->httpOnly,
            );
        }

        if ($res->redirectUrl !== false) {
            header("Location: " . $res->redirectUrl);
            return;
        }
        
        echo $res->body;
    }
}