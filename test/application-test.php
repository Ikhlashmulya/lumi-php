<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

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
    $page = $ctx->req->query('page');
    $ctx->res->text('Hello ' . $ctx->req->param('id') . ' middleware? ' . $ctx->get('fromMiddleware') . ' page? ' . $page ?? '');
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

$app->patch('/test/body', $testMiddleware, function (Context $ctx) {
    $body = $ctx->req->body();
    $ctx->res->json($body);
});

$app->post('/test/file', $testMiddleware, function (Context $ctx) {
    $file = $ctx->req->file('photo');
    $file->store('.');

    $ctx->json(['message' => "file {$file->getName()} uploaded"]);
});

$app->post('/test/files', $testMiddleware, function (Context $ctx) {
    $files = $ctx->req->file('files');
    foreach ($files as $file) {
        $file->store('.');
    }

    $ctx->json(['message' => "file uploaded"]);
});

$app->onError(function (\Throwable $e, Context $ctx) {
    $ctx->res->status(500)->json([
        'message' => $e->getMessage()
    ]);
});

$app->notFound(function (Context $ctx) {
    $ctx->res->json([
        'message' => 'Resource Not Found'
    ]);
});

$admin = $app->group('/admin');
$admin->get('/test', function (Context $ctx) {
    $ctx->res->text('Hello Admin');
});

$app->get('/throw', function (Context $ctx) {
    throw new ErrorException('Test error');
});

$app->run();
