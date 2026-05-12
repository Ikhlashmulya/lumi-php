<?php

use Lumi\LumiPHP\Http\Context;
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

test('Context throws when next is called multiple times from one handler', function () {
    $context = makeContext();
    $handler = function (Context $ctx) {
        $ctx->next();
        $ctx->next();
    };

    $context->setHandlers(0, [$handler]);

    assertThrows(RuntimeException::class, fn () => $handler($context));
});

