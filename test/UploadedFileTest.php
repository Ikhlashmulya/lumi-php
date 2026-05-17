<?php

use Lumi\LumiPHP\Factory\UploadedFileFactory;
use Lumi\LumiPHP\Http\Request;
use Lumi\LumiPHP\Http\UploadedFile;

test('UploadedFile stores uploaded file metadata', function () {
    $file = new UploadedFile(
        name: 'avatar.png',
        tmpName: '/tmp/php-upload',
        type: 'image/png',
        size: 1234,
        err: UPLOAD_ERR_OK
    );

    assertSameValue('avatar.png', $file->getName());
    assertSameValue('/tmp/php-upload', $file->getTmpName());
    assertSameValue('image/png', $file->getType());
    assertSameValue(1234, $file->getSize());
    assertSameValue(UPLOAD_ERR_OK, $file->getError());
    assertSameValue(true, $file->isValid());
});

test('UploadedFile is invalid when upload error is set', function () {
    $file = new UploadedFile(
        name: 'avatar.png',
        tmpName: '',
        type: 'image/png',
        size: 0,
        err: UPLOAD_ERR_NO_FILE
    );

    assertSameValue(false, $file->isValid());
});

test('UploadedFile store throws when directory does not exist', function () {
    $file = new UploadedFile(name: 'avatar.png');

    assertThrows(
        RuntimeException::class,
        fn () => $file->store(__DIR__ . '/missing-upload-directory')
    );
});

test('UploadedFileFactory creates single uploaded file from globals', function () {
    $_FILES = [
        'avatar' => [
            'name' => 'avatar.png',
            'tmp_name' => '/tmp/php-avatar',
            'type' => 'image/png',
            'size' => 1234,
            'error' => UPLOAD_ERR_OK,
        ],
    ];

    $files = UploadedFileFactory::fromGlobals();

    assertSameValue(true, isset($files['avatar']));
    assertSameValue(UploadedFile::class, $files['avatar']::class);
    assertSameValue('avatar.png', $files['avatar']->getName());
    assertSameValue('/tmp/php-avatar', $files['avatar']->getTmpName());
    assertSameValue('image/png', $files['avatar']->getType());
    assertSameValue(1234, $files['avatar']->getSize());
    assertSameValue(UPLOAD_ERR_OK, $files['avatar']->getError());
});

test('UploadedFileFactory creates multiple uploaded files from globals', function () {
    $_FILES = [
        'photos' => [
            'name' => ['first.png', 'second.jpg'],
            'tmp_name' => ['/tmp/php-first', '/tmp/php-second'],
            'type' => ['image/png', 'image/jpeg'],
            'size' => [1234, 5678],
            'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_OK],
        ],
    ];

    $files = UploadedFileFactory::fromGlobals();

    assertSameValue(true, isset($files['photos']));
    assertSameValue(2, count($files['photos']));
    assertSameValue('first.png', $files['photos'][0]->getName());
    assertSameValue('/tmp/php-first', $files['photos'][0]->getTmpName());
    assertSameValue('second.jpg', $files['photos'][1]->getName());
    assertSameValue('/tmp/php-second', $files['photos'][1]->getTmpName());
});

test('Request resolves uploaded files lazily and caches the result', function () {
    $calls = 0;
    $uploadedFile = new UploadedFile(name: 'avatar.png');
    $request = new Request(
        fileResolver: function () use (&$calls, $uploadedFile) {
            $calls++;

            return [
                'avatar' => $uploadedFile,
            ];
        }
    );

    assertSameValue(0, $calls);
    assertSameValue($uploadedFile, $request->file('avatar'));
    assertSameValue($uploadedFile, $request->file('avatar'));
    assertSameValue(1, $calls);
});

test('Request returns null when uploaded file is missing', function () {
    $request = new Request(fileResolver: fn () => []);

    assertSameValue(null, $request->file('avatar'));
});
