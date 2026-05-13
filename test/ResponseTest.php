<?php

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
