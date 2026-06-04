# Lumi PHP Documentation

## Development

Install dependencies:

```bash
composer install
```

Run one of the example applications:

```bash
php -S localhost:9000 -t example/basic
```

More examples are available in [example](example).

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

    return $ctx->text("User ID: $id");
});
```

To get all route parameters:

```php
$params = $ctx->req->param();
```

## Invokable Class Handlers

Route handlers can be closures, callable objects, or invokable class names. When a class name is passed, Lumi creates the class instance and calls its `__invoke()` method.

```php
use Lumi\LumiPHP\Http\Context;

class HomeController
{
    public function __invoke(Context $ctx)
    {
        return $ctx->text('Hello from controller');
    }
}

$app->get('/', HomeController::class);
```

Invokable classes can also be mixed with route middleware:

```php
class AuthMiddleware
{
    public function __invoke(Context $ctx)
    {
        $ctx->set('user', 'Lumi');

        return $ctx->next();
    }
}

$app->get('/profile', AuthMiddleware::class, function (Context $ctx) {
    return $ctx->text('Hello ' . $ctx->get('user'));
});
```

## Route Groups

Group routes under a shared path prefix:

```php
$api = $app->group('/api');

$api->get('/users', function (Context $ctx) {
    return $ctx->json([
        'users' => [],
    ]);
});
```

Groups can also receive middleware:

```php
$admin = $app->group('/admin', function (Context $ctx) {
    $ctx->set('area', 'admin');
    return $ctx->next();
});

$admin->get('/dashboard', function (Context $ctx) {
    return $ctx->text('Admin dashboard');
});
```

## Middleware

Global middleware runs before matched route handlers:

```php
$app->use(function (Context $ctx) {
    $ctx->set('fromMiddleware', 'global');
    return $ctx->next();
});
```

Middleware can also use invokable class names:

```php
class RequestIdMiddleware
{
    public function __invoke(Context $ctx)
    {
        $ctx->set('requestId', uniqid('req_', true));

        return $ctx->next();
    }
}

$app->use(RequestIdMiddleware::class);
```

Path-scoped middleware only runs when the request URI matches the prefix:

```php
$app->use('/users', function (Context $ctx) {
    $ctx->set('scope', 'users');
    return $ctx->next();
});

$app->get('/users/{id}', function (Context $ctx) {
    $scope = $ctx->get('scope');

    return $ctx->text("Matched scope: $scope");
});
```

### Middleware Execution Order

Matching middleware runs in the order it was registered, then the matched route handler runs last.

For a typical setup:

```php
$app->use(function (Context $ctx) {
    $ctx->set('global', true);
    return $ctx->next();
});

$admin = $app->group('/admin', function (Context $ctx) {
    $ctx->set('admin', true);
    return $ctx->next();
});

$admin->get('/dashboard', function (Context $ctx) {
    return $ctx->text('Admin dashboard');
});
```

A request to `/admin/dashboard` runs in this order:

```text
global middleware
admin group middleware
route handler
```

Middleware registered after a group is created will run after that group middleware if it also matches the request path:

```php
$admin = $app->group('/admin', $adminMiddleware);

$app->use('/admin', $auditMiddleware);

$admin->get('/dashboard', $handler);
```

Execution order:

```text
admin group middleware
audit middleware
route handler
```

Middleware can also run code after the next handler:

```php
$app->use(function (Context $ctx) {
    $result = $ctx->next();

    $ctx->header('X-After-Middleware', 'yes');

    return $result;
});
```

## Context

Handlers receive a `Context` instance:

```php
$app->get('/hello', function (Context $ctx) {
    $ctx->set('name', 'Lumi');

    return $ctx->text('Hello ' . $ctx->get('name'));
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
$ctx->setCookie(new Cookie('token', '123'));
```

Response shortcut methods such as `text()`, `json()`, `redirect()`, and `view()` return the response, so handlers can return them directly:

```php
$app->get('/health', function (Context $ctx) {
    return $ctx->json([
        'status' => 'ok',
    ]);
});
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

Read cookies:

```php
$token = $ctx->req->cookie('token');
```

## Response

Send plain text:

```php
return $ctx->text('Hello World');
```

Send JSON:

```php
return $ctx->json([
    'message' => 'Hello World',
]);
```

Set a response header:

```php
$ctx->header('X-App', 'Lumi');
```

Redirect to another URL:

```php
return $ctx->redirect('/login');
```

Set a status code:

```php
return $ctx->status(201)->json([
    'message' => 'Created',
]);
```

The response object is still available directly:

```php
$ctx->res->status(204);
```

## Cookies

Use `Cookie` to set cookies on the response:

```php
use Lumi\LumiPHP\Http\Cookie;

$app->get('/set-cookie', function (Context $ctx) {
    $ctx->setCookie(new Cookie('token', '123', expires: time() + 3600));

    return $ctx->text('Cookie has been set');
});
```

Read cookies from the request:

```php
$app->get('/get-cookie', function (Context $ctx) {
    $token = $ctx->req->cookie('token') ?? 'empty';

    return $ctx->text('Token: ' . $token);
});
```

Cookie constructor options:

```php
new Cookie(
    name: 'token',
    value: '123',
    expires: time() + 3600,
    path: '/',
    domain: '',
    secure: false,
    httpOnly: true
);
```

## Views

Set the view directory:

```php
$app->setView(__DIR__ . '/views');
```

Render a PHP view file:

```php
return $ctx->view('index', [
    'name' => 'Lumi',
]);
```

This will load:

```text
views/index.php
```

View data is extracted as local variables:

```php
<h1>Hello <?= htmlspecialchars($name) ?></h1>
```

## Error Handling

Customize the 404 response:

```php
$app->onNotFound(function (Context $ctx) {
    return $ctx->status(404)->json([
        'message' => 'Not found',
    ]);
});
```

Handle uncaught errors from route handlers and middleware:

```php
$app->onError(function (Throwable $error, Context $ctx) {
    return $ctx->status(500)->json([
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

- Session helpers
- Safe error handling with debug mode
- Logger
- Built-in middleware:
    - CSRF middleware
    - Request ID middleware
    - CORS middleware
    - request logger middleware
    - static file middleware

Under consideration:

- Config and environment support
- Request validation
- HTTP status response helpers
- Trusted proxy and real IP handling
- Content negotiation and Accept header helpers
- Response download and file response helpers
- Route naming and URL generation
- Simple dependency injection container
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
