<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;
use Lumi\LumiPHP\Http\Cookie;

$app = new Application();

$app->get('/', function (Context $ctx) {
    return $ctx->text('Open /set-cookie first, then /get-cookie');
});

$app->get('/set-cookie', function (Context $ctx) {
    $ctx->setCookie(new Cookie('token', '123', expires: time() + 3600));

    return $ctx->text('Cookie token has been set');
});

$app->get('/get-cookie', function (Context $ctx) {
    $token = $ctx->req->cookie('token') ?? 'empty';

    return $ctx->text('Token: ' . $token);
});

$app->run();
