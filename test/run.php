<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/TestCase.php';

$GLOBALS['tests'] = [];

require_once __DIR__ . '/PathUtilTest.php';
require_once __DIR__ . '/RouterTest.php';
require_once __DIR__ . '/RequestTest.php';
require_once __DIR__ . '/ResponseTest.php';
require_once __DIR__ . '/ContextTest.php';
require_once __DIR__ . '/ApplicationTest.php';
require_once __DIR__ . '/UploadedFileTest.php';

$passed = 0;

foreach ($GLOBALS['tests'] as $test) {
    try {
        $test['callback']();
        $passed++;
        echo ".";
    } catch (Throwable $e) {
        echo "\nFAILED: {$test['name']}\n";
        echo $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n$passed tests passed.\n";
