# Lumi PHP Framework

Lumi is a simple PHP framework for building simple web applications and APIs with expressive routing, middleware, request helpers, responses, and view rendering.

## Installation

Install Lumi with Composer:

```bash
composer require lumi/lumi-php
```

## Development

For local development, install dependencies first:

```bash
composer install
```

Run the sample application:

```bash
php -S localhost:9000 test/application-test.php
```

## Basic Usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

$app = new Application();

$app->get('/', function (Context $ctx) {
    $ctx->res->text('Hello World');
});

$app->run();
```

## Routing

Lumi supports common HTTP method helpers:

```php
$app->get('/users', $handler);
$app->post('/users', $handler);
$app->put('/users/{id}', $handler);
$app->patch('/users/{id}', $handler);
$app->delete('/users/{id}', $handler);
$app->options('/users', $handler);
$app->head('/users', $handler);
$app->trace('/users', $handler);
```

Route parameters can be read from the request object:

```php
$app->get('/users/{id}', function (Context $ctx) {
    $id = $ctx->req->param('id');

    $ctx->res->text("User ID: $id");
});
```

To get all route parameters:

```php
$params = $ctx->req->param();
```

## Route Groups

Group routes under a shared path prefix:

```php
$api = $app->group('/api');

$api->get('/users', function (Context $ctx) {
    $ctx->res->json([
        'users' => [],
    ]);
});
```

Groups can also receive middleware:

```php
$admin = $app->group('/admin', function (Context $ctx) {
    $ctx->set('area', 'admin');
    $ctx->next();
});

$admin->get('/dashboard', function (Context $ctx) {
    $ctx->res->text('Admin dashboard');
});
```

## Middleware

Global middleware runs before matched route handlers:

```php
$app->use(function (Context $ctx) {
    $ctx->set('fromMiddleware', 'global');
    $ctx->next();
});
```

Path-scoped middleware only runs when the request URI matches the prefix:

```php
$app->use('/users', function (Context $ctx) {
    $ctx->set('scope', 'users');
    $ctx->next();
});

$app->get('/users/{id}', function (Context $ctx) {
    $scope = $ctx->get('scope');

    $ctx->res->text("Matched scope: $scope");
});
```

## Context

Handlers receive a `Context` instance:

```php
$app->get('/hello', function (Context $ctx) {
    $ctx->set('name', 'Lumi');

    $ctx->res->text('Hello ' . $ctx->get('name'));
});
```

Available context properties:

```php
$ctx->req;
$ctx->res;
```

Available context methods:

```php
$ctx->next();
$ctx->set('key', 'value');
$ctx->get('key');
```

## Request

Read route parameters:

```php
$id = $ctx->req->param('id');
```

Read query values:

```php
$page = $ctx->req->query('page');
```

Read headers:

```php
$authorization = $ctx->req->header('Authorization');
```

Read JSON request body:

```php
$data = $ctx->req->json();
```

Read form request body:

```php
$data = $ctx->req->body();
```

## Response

Send plain text:

```php
$ctx->res->text('Hello World');
```

Send JSON:

```php
$ctx->res->json([
    'message' => 'Hello World',
]);
```

Set a response header:

```php
$ctx->res->header('X-App', 'Lumi');
```

Redirect to another URL:

```php
$ctx->res->redirect('/login');
```

Set a status code:

```php
$ctx->res->status(201)->json([
    'message' => 'Created',
]);
```

## Views

Set the view directory:

```php
$app->setView(__DIR__ . '/views');
```

Render a PHP view file:

```php
$ctx->res->view('index', [
    'name' => 'Lumi',
]);
```

This will load:

```text
views/index.php
```

View data is available through the `$_` variable:

```php
<h1>Hello <?= $_['name'] ?></h1>
```

## Error Handling

Customize the 404 response:

```php
$app->notFound(function (Context $ctx) {
    $ctx->res->status(404)->json([
        'message' => 'Not found',
    ]);
});
```

Handle uncaught errors from route handlers and middleware:

```php
$app->onError(function (Throwable $error, Context $ctx) {
    $ctx->res->status(500)->json([
        'message' => 'Internal server error',
    ]);
});
```

## Testing Applications

Use `handle()` to test routes without starting a PHP server:

```php
$res = $app->handle(new Request(
    method: 'GET',
    uri: '/users/42'
));

assert($res->statusCode === 200);
assert($res->body === '...');
```

## TODO

- Built-in middleware:
    - CORS middleware
    - request logger middleware
    - JSON body parser middleware
    - static file middleware

- File upload request helpers:

    ```php
    $file = $ctx->req->file('avatar');
    $files = $ctx->req->files();
    ```

- Route helpers for all methods:

    ```php
    $app->all('/health', $handler);
    $app->any('/webhook', $handler);
    ```

- Route parameter constraints:

    ```php
    $app->get('/users/{id:number}', $handler);
    $app->get('/posts/{slug}', $handler);
    ```

- Nested route groups:

    ```php
    $api = $app->group('/api');
    $v1 = $api->group('/v1');

    $v1->get('/users', $handler);
    ```

- Simple dependency injection container. (priority: low)
