<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Context;

$app = new Application();

$app->get('/', function (Context $ctx) {
    $ctx->res->text('Hello World');
});

$app->get('/users/{id}', function (Context $ctx) {
    $ctx->res->text('Hello ' . $ctx->req->getParam('id'));
});

$app->run();
