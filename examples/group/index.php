<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

$app = new Application();

$app->get('/', function (Context $ctx) {
    return $ctx->text('Open /admin/dashboard or /admin/users');
});

$admin = $app->group('/admin', function (Context $ctx) {
    $ctx->set('role', 'admin');

    return $ctx->next();
});

$admin->get('/dashboard', function (Context $ctx) {
    return $ctx->text('Dashboard for ' . $ctx->get('role'));
});

$admin->get('/users', function (Context $ctx) {
    return $ctx->json([
        'role' => $ctx->get('role'),
        'users' => ['Ikhlashmulya', 'Lumi'],
    ]);
});

$app->run();
