<?php

namespace Lumi\LumiPHP;

class Request {
    private static ?self $req = null;

    public static function getInstance(): self {
        if (self::$req === null) {
            self::$req = new self();
        }

        return self::$req;
    }
}