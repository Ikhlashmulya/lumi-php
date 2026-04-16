<?php

namespace Ikhlashmulya\RegularPHP;

class Context {
    public Request $req;
    public Response $res;

    public function __construct()
    {
        $this->req = Request::getInstance();
        $this->res = Response::getInstance();
    }
}