# Lumi PHP Framework

Lumi is a simple PHP framework for building web applications and APIs with expressive routing, middleware, request helpers, responses, and view rendering.

## Installation

Install Lumi with Composer:

```bash
composer require lumi/lumi-php
```

## Basic Usage

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use Lumi\LumiPHP\Application;
use Lumi\LumiPHP\Http\Context;

$app = new Application();

$app->get('/', function (Context $ctx) {
    return $ctx->text('Hello World');
});

$app->run();
```

Run the app with PHP's built-in server:

```bash
php -S localhost:9000 index.php
```

## Documentation

Read the full documentation in [docs.md](docs.md).
