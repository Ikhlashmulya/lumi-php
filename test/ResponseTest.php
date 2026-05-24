<?php

use Lumi\LumiPHP\Http\Cookie;
use Lumi\LumiPHP\Http\Response;

test('Response stores status code', function () {
    $response = new Response();

    $returned = $response->status(201);

    assertSameValue(201, $response->statusCode);
    assertSameValue($response, $returned);
});

test('Response stores custom headers', function () {
    $response = new Response();

    $response->header('X-Test', 'Lumi');

    assertSameValue(['X-Test' => 'Lumi'], $response->headers);
});

test('Response writes plain text body', function () {
    $response = new Response();

    $response->text('Hello Lumi');

    assertSameValue('text/plain; charset=utf-8', $response->headers['Content-Type']);
    assertSameValue('Hello Lumi', $response->body);
});

test('Response writes json body', function () {
    $response = new Response();

    $response->json(['name' => 'Lumi']);

    assertSameValue('application/json; charset=utf-8', $response->headers['Content-Type']);
    assertSameValue('{"name":"Lumi"}', $response->body);
});

test('Response stores redirect URL', function () {
    $response = new Response();

    $response->redirect('/login');

    assertSameValue(302, $response->statusCode);
    assertSameValue('/login', $response->redirectUrl);
});

test('Response renders views with data', function () {
    $response = new Response();
    $response->setView(__DIR__ . '/views');

    $response->view('index', ['name' => 'Lumi']);

    assertSameValue('text/html; charset=utf-8', $response->headers['Content-Type']);
    assertStringContains('<h2>author Lumi</h2>', $response->body);
});

test('Response throws exception when view file does not exist', function () {
    $response = new Response();
    $response->setView(__DIR__ . '/views');

    assertThrows(\RuntimeException::class, function () use ($response) {
        $response->view('nonexistent_view_file');
    });
});

test('Response with set cookies', function () {
    $response = new Response();

    $token = new Cookie('token', '123');
    $token2 = new Cookie('token2', '1234');

    $response->setCookie($token);
    $response->setCookie($token2);

    assertSameValue(2, count($response->cookies));
    assertSameValue($token, $response->cookies[0]);
    assertSameValue('token', $response->cookies[0]->name);
    assertSameValue('123', $response->cookies[0]->value);
    assertSameValue($token2, $response->cookies[1]);
    assertSameValue('token2', $response->cookies[1]->name);
    assertSameValue('1234', $response->cookies[1]->value);
});
