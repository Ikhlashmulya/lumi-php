<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ikhlashmulya\RegularPHP\Application;
use Ikhlashmulya\RegularPHP\Context;

$app = new Application();

$app->get('/', function (Context $ctx) {
    $ctx->res->text('Hello World');
});

$app->run();
