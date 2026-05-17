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
    $ctx->text('Hello World');
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

    $ctx->text("User ID: $id");
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
    $ctx->json([
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
    $ctx->text('Admin dashboard');
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

    $ctx->text("Matched scope: $scope");
});
```

## Context

Handlers receive a `Context` instance:

```php
$app->get('/hello', function (Context $ctx) {
    $ctx->set('name', 'Lumi');

    $ctx->text('Hello ' . $ctx->get('name'));
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
$ctx->status(201);
$ctx->header('X-App', 'Lumi');
$ctx->text('Hello World');
$ctx->json(['message' => 'Hello World']);
$ctx->redirect('/login');
$ctx->view('index', ['name' => 'Lumi']);
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

Read uploaded files:

```php
$file = $ctx->req->file('avatar');
$files = $ctx->req->files();
```

Move an uploaded file to a specific path:

```php
$file = $ctx->req->file('avatar');

if ($file !== null && $file->isValid()) {
    $file->moveTo(__DIR__ . '/uploads/' . $file->getName());
}
```

Store an uploaded file in a directory:

```php
$file = $ctx->req->file('avatar');

if ($file !== null && $file->isValid()) {
    $file->store(__DIR__ . '/uploads');
}
```

Store an uploaded file with a new filename while keeping the original extension:

```php
$file = $ctx->req->file('avatar');

if ($file !== null && $file->isValid()) {
    $file->store(__DIR__ . '/uploads', 'profile-picture');
}
```

Uploaded file helpers:

```php
$file->getName();
$file->getTmpName();
$file->getType();
$file->getSize();
$file->getError();
$file->isValid();
$file->moveTo($path);
$file->store($dir, $newName = '');
```

## Response

Send plain text:

```php
$ctx->text('Hello World');
```

Send JSON:

```php
$ctx->json([
    'message' => 'Hello World',
]);
```

Set a response header:

```php
$ctx->header('X-App', 'Lumi');
```

Redirect to another URL:

```php
$ctx->redirect('/login');
```

Set a status code:

```php
$ctx->status(201)->json([
    'message' => 'Created',
]);
```

The response object is still available directly:

```php
$ctx->res->status(204);
```

## Views

Set the view directory:

```php
$app->setView(__DIR__ . '/views');
```

Render a PHP view file:

```php
$ctx->view('index', [
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
    $ctx->status(404)->json([
        'message' => 'Not found',
    ]);
});
```

Handle uncaught errors from route handlers and middleware:

```php
$app->onError(function (Throwable $error, Context $ctx) {
    $ctx->status(500)->json([
        'message' => 'Internal server error',
    ]);
});
```

## Testing Applications

Use `handle()` to test routes without starting a PHP server:

```php
use Lumi\LumiPHP\Http\Request;

$res = $app->handle(new Request(
    method: 'GET',
    uri: '/users/42'
));

assert($res->statusCode === 200);
assert($res->body === '...');
```

## TODO

- Add feature for handle cookie

- Built-in middleware:
    - CORS middleware
    - request logger middleware
    - JSON body parser middleware
    - static file middleware

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
