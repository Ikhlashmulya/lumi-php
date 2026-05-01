<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Context;

$app = new Application();

$app->use(function (Context $ctx) {
    $ctx->set('fromMiddleware', 'global');
    $ctx->next();
});

$app->use('/users', function (Context $ctx) {
    $ctx->set('fromMiddleware', 'users');
    $ctx->next();
});

$app->setView(__DIR__ . '/views');

$app->get('/', function (Context $ctx) {
    $ctx->res->text('Hello World' . ' middleware? ' . $ctx->get('fromMiddleware'));
});

$app->get('/test/redirect', function (Context $ctx) {
    $ctx->res->redirect('/');
});

$app->get('/users/{id}', function (Context $ctx) {
    $ctx->res->text('Hello ' . $ctx->req->getParam('id') . ' middleware? ' . $ctx->get('fromMiddleware'));
});

$app->post('/users', function (Context $ctx) {
    $data = $ctx->req->json();

    $ctx->res->status(201)->json([
        'message' => 'Hello ' . $data['name']
    ]);
});

$app->get('/test/view', function (Context $ctx) {
    $name = 'ikhlashmulya';

    $ctx->res->view('index', compact('name'));
});

$testMiddleware = function(Context $ctx) {
    $ctx->set('username', 'ikhlashmulya');
    $ctx->next();
};

$app->get('/test/middleware', $testMiddleware, function (Context $ctx) {
    $name = $ctx->get('username');
    $ctx->res->text("hello $name");
});

$app->run();
