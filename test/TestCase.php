<?php

function test(string $name, callable $callback): void
{
    $GLOBALS['tests'][] = [
        'name' => $name,
        'callback' => $callback,
    ];
}

function assertSameValue(mixed $expected, mixed $actual, string $message = ''): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            ($message === '' ? 'Values are not the same' : $message)
            . "\nExpected: " . var_export($expected, true)
            . "\nActual:   " . var_export($actual, true)
        );
    }
}

function assertThrows(string $expectedException, callable $callback, string $message = ''): void
{
    try {
        $callback();
    } catch (Throwable $e) {
        if ($e instanceof $expectedException) {
            return;
        }

        throw new RuntimeException(
            ($message === '' ? 'Unexpected exception type' : $message)
            . "\nExpected: $expectedException"
            . "\nActual:   " . $e::class
        );
    }

    throw new RuntimeException(
        ($message === '' ? 'Expected exception was not thrown' : $message)
        . "\nExpected: $expectedException"
    );
}

