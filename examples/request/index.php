<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

$app = new Application();

$app->get('/', function (Context $ctx) {
    return $ctx->text('Try POST /users, POST /users/json, or PATCH /users/42');
});

$app->post('/users', function (Context $ctx) {
    $body = $ctx->req->body();

    return $ctx->status(201)->json([
        'name' => $body['name'] ?? null,
        'source' => 'form',
    ]);
});

$app->post('/users/json', function (Context $ctx) {
    $data = $ctx->req->json();

    return $ctx->status(201)->json([
        'name' => $data['name'] ?? null,
        'source' => 'json',
    ]);
});

$app->patch('/users/{id}', function (Context $ctx) {
    return $ctx->json([
        'id' => $ctx->req->param('id'),
        'body' => $ctx->req->body(),
    ]);
});

$app->run();
