<?php

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;
use Lumi\LumiPHP\Http\Request;

function makeRequest(string $method, string $uri, array $queries = [], array $body = [], string $rawBody = ''): Request
{
    return new Request(
        method: $method,
        uri: $uri,
        queries: $queries,
        rawBody: $rawBody,
        parseBody: $body
    );
}

test('Application returns text response from matching route', function () {
    $app = new Application();
    $app->get('/', function (Context $ctx) {
        $ctx->res->text('Hello World');
    });

    $res = $app->handle(makeRequest('GET', '/'));

    assertSameValue(200, $res->statusCode);
    assertSameValue('text/plain; charset=utf-8', $res->headers['Content-Type']);
    assertSameValue('Hello World', $res->body);
});

test('Application returns route parameters to handlers', function () {
    $app = new Application();
    $app->get('/users/{id}', function (Context $ctx) {
        $ctx->res->text('User ' . $ctx->req->param('id'));
    });

    $res = $app->handle(makeRequest('GET', '/users/42'));

    assertSameValue('User 42', $res->body);
});

test('Application returns query values to handlers', function () {
    $app = new Application();
    $app->get('/search', function (Context $ctx) {
        $ctx->res->text('Page ' . $ctx->req->query('page'));
    });

    $res = $app->handle(makeRequest('GET', '/search', ['page' => '2']));

    assertSameValue('Page 2', $res->body);
});

test('Application returns parsed body values to handlers', function () {
    $app = new Application();
    $app->post('/users', function (Context $ctx) {
        $body = $ctx->req->body();

        $ctx->res->json(['name' => $body['name']]);
    });

    $res = $app->handle(makeRequest('POST', '/users', body: ['name' => 'Lumi']));

    assertSameValue('application/json; charset=utf-8', $res->headers['Content-Type']);
    assertSameValue('{"name":"Lumi"}', $res->body);
});

test('Application returns json request body to handlers', function () {
    $app = new Application();
    $app->post('/users/json', function (Context $ctx) {
        $data = $ctx->req->json();

        $ctx->res->text($data['name']);
    });

    $res = $app->handle(makeRequest('POST', '/users/json', rawBody: '{"name":"Lumi"}'));

    assertSameValue('Lumi', $res->body);
});

test('Application runs middleware before route handler', function () {
    $app = new Application();
    $app->use(function (Context $ctx) {
        $ctx->set('name', 'Lumi');
        $ctx->next();
    });
    $app->get('/', function (Context $ctx) {
        $ctx->res->text('Hello ' . $ctx->get('name'));
    });

    $res = $app->handle(makeRequest('GET', '/'));

    assertSameValue('Hello Lumi', $res->body);
});

test('Application only runs middleware for matching path prefix', function () {
    $app = new Application();
    $app->use('/admin', function (Context $ctx) {
        $ctx->res->text('Admin middleware');
    });
    $app->get('/users', function (Context $ctx) {
        $ctx->res->text('Users route');
    });

    $res = $app->handle(makeRequest('GET', '/users'));

    assertSameValue('Users route', $res->body);
});

test('Application returns default not found response', function () {
    $app = new Application();

    $res = $app->handle(makeRequest('GET', '/missing'));

    assertSameValue(404, $res->statusCode);
    assertSameValue('text/plain; charset=utf-8', $res->headers['Content-Type']);
    assertSameValue('Url Not Found', $res->body);
});

test('Application uses custom not found handler', function () {
    $app = new Application();
    $app->notFound(function (Context $ctx) {
        $ctx->res->json(['error' => 'missing']);
    });

    $res = $app->handle(makeRequest('GET', '/missing'));

    assertSameValue(404, $res->statusCode);
    assertSameValue('application/json; charset=utf-8', $res->headers['Content-Type']);
    assertSameValue('{"error":"missing"}', $res->body);
});

test('Application returns default error response when handler throws', function () {
    $app = new Application();
    $app->get('/', function () {
        throw new RuntimeException('Boom');
    });

    $res = $app->handle(makeRequest('GET', '/'));

    assertSameValue(500, $res->statusCode);
    assertSameValue('text/plain; charset=utf-8', $res->headers['Content-Type']);
    assertSameValue('Internal Server Error', $res->body);
});

test('Application uses custom error handler when handler throws', function () {
    $app = new Application();
    $app->onError(function (Throwable $e, Context $ctx) {
        $ctx->res->status(500)->json(['error' => $e->getMessage()]);
    });
    $app->get('/', function () {
        throw new RuntimeException('Boom');
    });

    $res = $app->handle(makeRequest('GET', '/'));

    assertSameValue(500, $res->statusCode);
    assertSameValue('application/json; charset=utf-8', $res->headers['Content-Type']);
    assertSameValue('{"error":"Boom"}', $res->body);
});

test('Application passes view path to response', function () {
    $app = new Application();
    $app->setView(__DIR__ . '/views');
    $app->get('/', function (Context $ctx) {
        $ctx->res->view('index', ['name' => 'Lumi']);
    });

    $res = $app->handle(makeRequest('GET', '/'));

    assertSameValue('text/html; charset=utf-8', $res->headers['Content-Type']);
    assertStringContains('<h2>author Lumi</h2>', $res->body);
});

test('Application matches routes inside groups', function () {
    $app = new Application();
    $admin = $app->group('/admin', function (Context $ctx) {
        $ctx->set('role', 'admin');
        $ctx->next();
    });
    $admin->get('/dashboard', function (Context $ctx) {
        $ctx->res->text('Hello ' . $ctx->get('role'));
    });

    $res = $app->handle(makeRequest('GET', '/admin/dashboard'));

    assertSameValue('Hello admin', $res->body);
});
