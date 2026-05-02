# Lumi PHP Framework

Lumi is a tiny PHP framework for learning and experimenting how routing, middleware, and HTTP abstractions work internally.

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
use Lumi\LumiPHP\Context;

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
    $id = $ctx->req->getParam('id');

    $ctx->res->text("User ID: $id");
});
```

To get all route parameters:

```php
$params = $ctx->req->getParam();
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

Read headers:

```php
$authorization = $ctx->req->header('Authorization');
```

Read JSON request body:

```php
$data = $ctx->req->json();
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

## TODO

- Route groups with GoFiber-style API:

    ```php
    $api = $app->group('/api');
    $api->get('/users', $handler);

    $admin = $app->group('/admin', $authMiddleware);
    $admin->get('/dashboard', $handler);
    ```

- Not found and error handlers:

    ```php
    $app->notFound($handler);
    $app->onError($handler);
    ```

- Built-in middleware:
    - CORS middleware
    - request logger middleware
    - JSON body parser middleware
    - static file middleware

- Testing utility:

    ```php
    $response = $app->test('GET', '/users/1');
    ```

- Simple dependency injection container. (priority: low)
