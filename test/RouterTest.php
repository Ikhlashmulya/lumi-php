<?php

use Lumi\LumiPHP\Router;

test('Router matches a registered route and returns parameter values', function () {
    $router = new Router();
    $handler = fn () => null;

    $router->add('GET', '/users/{id}', $handler);

    assertSameValue(
        ['/users/{id}', ['42'], [$handler]],
        $router->match('GET', '/users/42')
    );
});

test('Router returns null values when no route matches', function () {
    $router = new Router();

    $router->add('GET', '/users/{id}', fn () => null);

    assertSameValue([null, null, null], $router->match('POST', '/users/42'));
});

test('Router includes matching middleware before route handlers', function () {
    $router = new Router();
    $globalMiddleware = fn () => null;
    $userMiddleware = fn () => null;
    $handler = fn () => null;

    $router->addMiddleware('/', $globalMiddleware);
    $router->addMiddleware('/users', $userMiddleware);
    $router->add('GET', '/users/{id}', $handler);

    assertSameValue(
        ['/users/{id}', ['42'], [$globalMiddleware, $userMiddleware, $handler]],
        $router->match('GET', '/users/42')
    );
});

