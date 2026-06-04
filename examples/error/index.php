<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

$app = new Application();

$app->onNotFound(function (Context $ctx) {
    return $ctx->json([
        'message' => 'Resource Not Found',
    ]);
});

$app->onError(function (Throwable $e, Context $ctx) {
    return $ctx->status(500)->json([
        'message' => $e->getMessage(),
    ]);
});

$app->get('/', function (Context $ctx) {
    return $ctx->text('Open /throw to see custom error handler');
});

$app->get('/throw', function () {
    throw new RuntimeException('Example error');
});

$app->run();
