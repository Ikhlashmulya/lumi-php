<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

$app = new Application();

$app->setView(__DIR__ . '/views');

$app->get('/', function (Context $ctx) {
    return $ctx->view('profile', [
        'name' => 'Ikhlashmulya',
        'framework' => 'Lumi PHP',
    ]);
});

$app->run();
