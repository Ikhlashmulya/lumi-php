<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

$app = new Application();

class RequestIdMiddleware
{
    public function __invoke(Context $ctx)
    {
        $ctx->set('requestId', uniqid('req_', true));

        return $ctx->next();
    }
}

$app->use(RequestIdMiddleware::class);

$app->use(function (Context $ctx) {
    $ctx->set('appName', 'Lumi Example');

    $response = $ctx->next();
    $ctx->header('X-Powered-By', 'Lumi PHP');

    return $response;
});

$app->use('/admin', function (Context $ctx) {
    $ctx->set('area', 'admin');

    return $ctx->next();
});

$onlyThisRoute = function (Context $ctx) {
    $ctx->set('routeMiddleware', 'active');

    return $ctx->next();
};

$app->get('/', function (Context $ctx) {
    return $ctx->json([
        'app' => $ctx->get('appName'),
        'requestId' => $ctx->get('requestId'),
    ]);
});

$app->get('/admin/dashboard', function (Context $ctx) {
    return $ctx->json([
        'area' => $ctx->get('area'),
        'requestId' => $ctx->get('requestId'),
    ]);
});

$app->get('/profile', $onlyThisRoute, function (Context $ctx) {
    return $ctx->json([
        'routeMiddleware' => $ctx->get('routeMiddleware'),
    ]);
});

$app->run();
