<?php

use Lumi\LumiPHP\Http\Request;

test('Request returns all route parameters', function () {
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $request = new Request('/users/{id}/posts/{postId}', ['42', '99']);

    assertSameValue(
        ['id' => '42', 'postId' => '99'],
        $request->param()
    );
});

test('Request returns one route parameter by key', function () {
    $_SERVER['REQUEST_METHOD'] = 'GET';

    $request = new Request('/users/{id}', ['42']);

    assertSameValue('42', $request->param('id'));
    assertSameValue(null, $request->param('missing'));
});

test('Request reads query values from request data', function () {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_REQUEST['page'] = '2';

    $request = new Request('/users', []);

    assertSameValue('2', $request->query('page'));
    assertSameValue(null, $request->query('missing'));

    unset($_REQUEST['page']);
});

