<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

$app = new Application();

$app->get('/', function (Context $ctx) {
    return $ctx->text('Hello from Lumi PHP');
});

$app->get('/hello/{name}', function (Context $ctx) {
    return $ctx->text('Hello ' . $ctx->req->param('name'));
});

$app->get('/search', function (Context $ctx) {
    $keyword = $ctx->req->query('q') ?? 'empty';

    return $ctx->json([
        'keyword' => $keyword,
    ]);
});

$app->get('/redirect-home', function (Context $ctx) {
    return $ctx->redirect('/');
});

$app->run();
