<?php

use Lumi\LumiPHP\Http\Request;

test('Request returns all route parameters', function () {
    $request = new Request(
        path: '/users/{id}/posts/{postId}',
        matches: ['42', '99']
    );

    assertSameValue(
        ['id' => '42', 'postId' => '99'],
        $request->param()
    );
});

test('Request returns one route parameter by key', function () {
    $request = new Request(
        path: '/users/{id}',
        matches: ['42']
    );

    assertSameValue('42', $request->param('id'));
    assertSameValue(null, $request->param('missing'));
});

test('Request reads query values from request data', function () {
    $request = new Request(
        path: '/users',
        queries: ['page' => '2']
    );

    assertSameValue('2', $request->query('page'));
    assertSameValue(null, $request->query('missing'));
});

test('Request reads header values', function () {
    $request = new Request(
        path: '/users',
        headers: ['Accept' => 'application/json']
    );

    assertSameValue('application/json', $request->header('Accept'));
    assertSameValue(null, $request->header('Missing'));
});

test('Request reads parsed body values', function () {
    $request = new Request(
        path: '/users',
        parseBody: ['name' => 'Lumi']
    );

    assertSameValue(['name' => 'Lumi'], $request->body());
});

test('Request decodes json body', function () {
    $request = new Request(
        path: '/users',
        rawBody: '{"name":"Lumi"}'
    );

    assertSameValue(['name' => 'Lumi'], $request->json());
});
