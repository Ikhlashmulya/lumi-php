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

function assertStringContains(string $needle, string $haystack, string $message = ''): void
{
    if (! str_contains($haystack, $needle)) {
        throw new RuntimeException(
            ($message === '' ? 'String does not contain expected value' : $message)
            . "\nExpected to contain: " . var_export($needle, true)
            . "\nActual:              " . var_export($haystack, true)
        );
    }
}
