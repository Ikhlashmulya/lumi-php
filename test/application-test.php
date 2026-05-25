<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;
use Lumi\LumiPHP\Http\Cookie;

$app = new Application([
    'debug' => true
]);

$app->use(function (Context $ctx) {
    $ctx->set('fromMiddleware', 'global');
    return $ctx->next();
});

$app->use('/users', function (Context $ctx) {
    $ctx->set('fromMiddleware', 'users');
    return $ctx->next();
});

$app->setView(__DIR__ . '/views');

$app->get('/', function (Context $ctx) {
    return $ctx->res->text('Hello World' . ' middleware? ' . $ctx->get('fromMiddleware'));
});

$app->get('/test/redirect', function (Context $ctx) {
    return $ctx->res->redirect('/');
});

$app->get('/users/{id}', function (Context $ctx) {
    $page = $ctx->req->query('page');
    return $ctx->res->text('Hello ' . $ctx->req->param('id') . ' middleware? ' . $ctx->get('fromMiddleware') . ' page? ' . $page ?? '');
});

$app->post('/users', function (Context $ctx) {
    $data = $ctx->req->json();

    return $ctx->res->status(201)->json([
        'message' => 'Hello ' . $data['name']
    ]);
});

$app->get('/test/view', function (Context $ctx) {
    $name = 'ikhlashmulya';

    return $ctx->res->view('index', compact('name'));
});

$testMiddleware = function(Context $ctx) {
    $ctx->set('username', 'ikhlashmulya');
    return $ctx->next();
};

$app->get('/test/middleware', $testMiddleware, function (Context $ctx) {
    $name = $ctx->get('username');
    return $ctx->res->text("hello $name");
});

$app->patch('/test/body', $testMiddleware, function (Context $ctx) {
    $body = $ctx->req->body();
    return $ctx->res->json($body);
});

$app->post('/test/file', $testMiddleware, function (Context $ctx) {
    $file = $ctx->req->file('photo');
    $file->store('.');

    return $ctx->json(['message' => "file {$file->getName()} uploaded"]);
});

$app->post('/test/files', $testMiddleware, function (Context $ctx) {
    $files = $ctx->req->file('files');
    foreach ($files as $file) {
        $file->store('.');
    }

    return $ctx->json(['message' => "file uploaded"]);
});

$app->get('/test/setcookie', function (Context $ctx) {
    $ctx->setCookie(new Cookie('token', '123', expires: time() + 1000));

    return $ctx->text('success set cookie');
});

$app->get('/test/getcookie', function (Context $ctx) {
    $token = $ctx->req->cookie('token');

    return $ctx->text('success get cookie' . $token);
});

// $app->onError(function (\Throwable $e, Context $ctx) {
//     return $ctx->res->status(500)->json([
//         'message' => $e->getMessage()
//     ]);
// });

$app->onNotFound(function (Context $ctx) {
    return $ctx->res->json([
        'message' => 'Resource Not Found'
    ]);
});

$admin = $app->group('/admin');
$admin->get('/test', function (Context $ctx) {
    return $ctx->res->text('Hello Admin');
});

$app->get('/throw', function (Context $ctx) {
    throw new ErrorException('Test error');
});

$app->run();
