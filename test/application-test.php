<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Context;

$app = new Application();

$app->setView(__DIR__ . '/views');

$app->get('/', function (Context $ctx) {
    $ctx->res->text('Hello World');
});

$app->get('/users/{id}', function (Context $ctx) {
    $ctx->res->text('Hello ' . $ctx->req->getParam('id'));
});

$app->post('/users', function (Context $ctx) {
    $data = $ctx->req->json();

    $ctx->res->json([
        'message' => 'Hello ' . $data['name']
    ]);
});

$app->get('/test/view', function (Context $ctx) {
    $name = 'ikhlashmulya';

    $ctx->res->view('index', compact('name'));
});

$app->run();
