# Lumi PHP Framework

A minimalist PHP framework inspired by hono js.

## Current Development Flow

```
php -s localhost:9000 test/application-test.php
```

## TODO

- Method Support: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`.
- Built-in JSON & Data Binding:
    
    Auto-parse JSON from request body `$ctx->req->json()`
    
    Auto-parse JSON response body `$ctx->res->json($data)`

- View html Support:

    ```
    $app->setView(__DIR__ . '/view');

    $ctx->res->view('index');
    ```

- Middleware Stack Support:

    ```
    $app->use(function($ctx) {
        $ctx->next();
    });
    ```
- Other helper methods

    - `$ctx->res->redirect('/login')`
    - `$ctx->res->status(404)->text('Not Found')`
    - `$ctx->res->header('Content-Type', 'application/json')`