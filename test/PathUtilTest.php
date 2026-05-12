<?php

use Lumi\LumiPHP\Helper\PathUtil;

test('PathUtil gets route parameter names', function () {
    assertSameValue(
        ['id', 'postId'],
        PathUtil::getParamNames('/users/{id}/posts/{postId}')
    );
});

test('PathUtil converts route parameters to regex groups', function () {
    assertSameValue(
        '/users/([a-zA-Z0-9]+)/posts/([a-zA-Z0-9]+)',
        PathUtil::toRegexPath('/users/{id}/posts/{postId}')
    );
});

test('PathUtil returns URI parameter values when route matches', function () {
    assertSameValue(
        ['123', 'abc'],
        PathUtil::isMatch('/users/{id}/posts/{postId}', '/users/123/posts/abc')
    );
});

test('PathUtil returns null when route does not match', function () {
    assertSameValue(null, PathUtil::isMatch('/users/{id}', '/posts/123'));
});

test('PathUtil treats root path as a prefix for all URIs', function () {
    assertSameValue(true, PathUtil::isPrefixMatch('/', '/anything'));
});

test('PathUtil matches child URI paths', function () {
    assertSameValue(true, PathUtil::isPrefixMatch('/users', '/users/123'));
});

test('PathUtil does not match similar path names', function () {
    assertSameValue(false, PathUtil::isPrefixMatch('/users', '/usernames/123'));
});
