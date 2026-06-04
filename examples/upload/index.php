<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

$app = new Application();

$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$app->get('/', function (Context $ctx) {
    return $ctx->text('POST /avatar with field photo, or POST /gallery with field files[]');
});

$app->post('/avatar', function (Context $ctx) use ($uploadDir) {
    $file = $ctx->req->file('photo');

    if ($file === null || !$file->isValid()) {
        return $ctx->status(400)->json([
            'message' => 'Photo is required',
        ]);
    }

    $file->store($uploadDir);

    return $ctx->json([
        'message' => 'Photo uploaded',
        'file' => $file->getName(),
    ]);
});

$app->post('/gallery', function (Context $ctx) use ($uploadDir) {
    $files = $ctx->req->file('files') ?? [];
    $uploaded = [];

    foreach ($files as $file) {
        if ($file->isValid()) {
            $file->store($uploadDir);
            $uploaded[] = $file->getName();
        }
    }

    return $ctx->json([
        'message' => 'Files uploaded',
        'files' => $uploaded,
    ]);
});

$app->run();
