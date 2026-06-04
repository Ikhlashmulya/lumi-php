# Changelog

All notable changes to Lumi will be documented in this file.

## v0.7.0

- Add support for invokable class route handlers using class name strings.
- Add support for invokable class middleware in global middleware, path-scoped middleware, route middleware, and route groups.
- Update router contracts to accept mixed handlers so class name handlers can be registered through `Application`, `RouterGroup`, and `RouterInterface`.
- Refactor application dispatching into smaller handler resolution, not found, error, and response conversion helpers.
- Update `Request::json()` to return an empty array for empty or invalid JSON bodies. by [@muhmuslimabdulj](https://github.com/muhmuslimabdulj)

- Add application tests for invokable class route handlers, global middleware, and route middleware.
- Add request tests for empty and invalid JSON bodies.
- Move manual application examples from `test/application-test.php` into the `examples` directory.
- Add feature-focused examples for basic routing, middleware, request bodies, views, uploads, cookies, route groups, and error handling.
- Split documentation into a shorter `README.md` and full `docs.md`.
- Update documentation for cookies and invokable class handlers.
- Remove the explicit Composer `version` field from `composer.json`. by [@muhmuslimabdulj](https://github.com/muhmuslimabdulj)


## v0.6.0

- Add request and response cookie handling.
- Add `Cookie` value object for response cookies.
- Update view templates from `$_['name']` style access to `$name` style variables.
- Update README: middleware execution order documentation and TODO.
- Rename uploaded file extension variable spelling. by [@muhmuslimabdulj](https://github.com/muhmuslimabdulj)
- Throw an exception when a requested view file does not exist. by [@muhmuslimabdulj](https://github.com/muhmuslimabdulj)
- Add and update tests for request cookies, response cookies, context cookie shortcuts, and application cookie handling.


## v0.5.0

- Rename `notFound()` to `onNotFound()` for consistency with `onError()`.
- Support returning `Response` instances from route handlers, middleware, `onNotFound` handlers, and `onError` handlers.
- Update `Context::next()` to return the next handler result.
- Update README examples to use returned responses and `onNotFound()`.
- Update tests to cover returned responses from handlers and middleware after-next behavior.


## v0.4.0

- Add `Context` response shortcuts for status, header, text, JSON, redirect, and view responses.
- Add uploaded file support with `UploadedFile` and `UploadedFileFactory`.
- Add lazy request file resolving through `Request::file()` and `Request::files()`.
- Add uploaded file helpers for metadata, validation, moving, and storing files.
- Update PHP request factory to provide uploaded files through a lazy resolver.
- Update sample application and README documentation for context shortcuts and file uploads.
- Add unit tests for context response shortcuts and uploaded file handling.


## v0.3.0

- Add `Application::handle()` flow that accepts a `Request` and returns a `Response`.
- Add `Request::withRoute()` for attaching route path and matches after routing.
- Add application unit tests for route handling, middleware, request data, not found responses, error handling, view rendering, and route groups.
- Add response unit tests and string contains test assertion helper.
- Move HTTP request, response, and context classes under the `Http` namespace.
- Move routing classes under the `Routing` namespace.
- Update request factory to create requests without route match data.
- Create fresh `Response` instances per handled request and pass configured view path into them.
- Fix view rendering to use `require` instead of `require_once` so templates can render multiple times in one PHP process.
- Update README documentation.


## v0.2.0

- Add route group support with shared path prefixes and group middleware.
- Add custom not found handler support.
- Add custom error handler support for uncaught handler and middleware exceptions.
- Add `RouterInterface` as a shared contract for `Application` and `RouterGroup`.
- Add unit test runner and tests for routing, path utilities, request helpers, and context behavior.
- Add Composer test script.
- Fix `Context::next()` handler index handling.
- Update README documentation for route groups, error handling, request helpers, response helpers, and roadmap TODOs.
- Update package description.


## v0.1.1

- Update `composer.json`: add license and require PHP `>=8.4`.


## v0.1.0
- Add support for `POST`, `PUT`, `PATCH`, `DELETE`, `OPTIONS`, `HEAD`, and `TRACE` HTTP methods.
- Add global and path-scoped middleware support.
- Add middleware/handler chaining with `Context::next()`.
- Add request helpers for query params, headers, form body, and JSON body.
- Add response helpers for JSON, status code, redirect, headers, and PHP views.
- Add view directory configuration with `setView()`.


## v0.0.1
- Add handling for the `GET` HTTP method.
- Add route path parameter handling.
- Add plain text response handling.
