<?php

use Lumi\LumiPHP\Http\Context;
use Lumi\LumiPHP\Http\Cookie;
use Lumi\LumiPHP\Http\Request;
use Lumi\LumiPHP\Http\Response;

function makeContext(): Context
{
    $_SERVER['REQUEST_METHOD'] = 'GET';

    return new Context(new Request('/', []), new Response());
}

test('Context stores and returns custom values', function () {
    $context = makeContext();

    $context->set('name', 'Lumi');

    assertSameValue('Lumi', $context->get('name'));
});

test('Context next runs the next handler in order', function () {
    $context = makeContext();
    $calls = [];

    $first = function (Context $ctx) use (&$calls) {
        $calls[] = 'first-before';
        $ctx->next();
        $calls[] = 'first-after';
    };

    $second = function () use (&$calls) {
        $calls[] = 'second';
    };

    $context->setHandlers(0, [$first, $second]);

    $first($context);

    assertSameValue(['first-before', 'second', 'first-after'], $calls);
});

test('Context next returns the next handler result', function () {
    $context = makeContext();
    $response = new Response();

    $first = function (Context $ctx) {
        return $ctx->next();
    };

    $second = function () use ($response) {
        return $response;
    };

    $context->setHandlers(0, [$first, $second]);

    assertSameValue($response, $first($context));
});

test('Context throws when next is called multiple times from one handler', function () {
    $context = makeContext();
    $handler = function (Context $ctx) {
        $ctx->next();
        $ctx->next();
    };

    $context->setHandlers(0, [$handler]);

    assertThrows(RuntimeException::class, fn () => $handler($context));
});

test('Context response shortcuts write to the response', function () {
    $context = makeContext();

    $returned = $context
        ->status(201)
        ->header('X-Test', 'Lumi');

    $response = $context->json(['message' => 'Created']);

    assertSameValue($context, $returned);
    assertSameValue($context->res, $response);
    assertSameValue(201, $context->res->statusCode);
    assertSameValue('Lumi', $context->res->headers['X-Test']);
    assertSameValue('application/json; charset=utf-8', $context->res->headers['Content-Type']);
    assertSameValue('{"message":"Created"}', $context->res->body);
});

test('Context redirect shortcut writes to the response', function () {
    $context = makeContext();

    $response = $context->redirect('/login');

    assertSameValue($context->res, $response);
    assertSameValue(302, $context->res->statusCode);
    assertSameValue('/login', $context->res->redirectUrl);
});

test('Context response shortcut set cookies', function () {
    $context = makeContext();

    $token = new Cookie('token', '123');
    $token2 = new Cookie('token2', '1234');

    $context->setCookie($token);
    $context->setCookie($token2);

    assertSameValue(2, count($context->res->cookies));
    assertSameValue($token, $context->res->cookies[0]);
    assertSameValue('token', $context->res->cookies[0]->name);
    assertSameValue('123', $context->res->cookies[0]->value);
    assertSameValue($token2, $context->res->cookies[1]);
    assertSameValue('token2', $context->res->cookies[1]->name);
    assertSameValue('1234', $context->res->cookies[1]->value);
});
