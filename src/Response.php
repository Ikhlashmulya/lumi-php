<?php

namespace Lumi\LumiPHP;

class Response {
    private static ?self $res = null;

    public static function getInstance(): self {
        if (self::$res === null) {
            self::$res = new self();
        }

        return self::$res;
    }

    public function text(string $text) {
        echo $text;
    }
}