# Contributing

Lumi started as a for-fun project: a small space to explore, learn, and build
something enjoyable with PHP.

That said, contributions are very welcome. If you find something to improve,
want to fix a bug, add a feature, improve documentation, or just make the
project a little nicer to use, thank you for taking the time to help.

This project keeps the contribution rules simple:

- Every code change must include or update unit tests.
- All tests must pass before committing.
- Commit messages should follow Conventional Commits.

Example commit messages:

```text
feat: add file upload helper
fix: prevent response state leak
test: add request factory tests
docs: update contributing guide
```

## Running Tests

Run the test suite with Composer:

```bash
composer test
```

Use this before opening a pull request or pushing a branch.

## Unit Test Rule

Any behavior change should have a test.

Good examples:

```text
test/RequestTest.php
test/ResponseTest.php
test/RouterTest.php
test/ApplicationTest.php
```

For small documentation-only changes, tests are not required.

## Commit Message Rule

Commit messages should use this format:

```text
type: short description
```

Recommended types:

```text
feat
fix
docs
test
refactor
```

Examples:

```text
feat: add file upload helper
fix: handle empty response body
test: add response emitter tests
docs: add Swoole research notes
```

If you need to reference an issue or branch, put it in the description or pull request instead of making it the commit prefix.

