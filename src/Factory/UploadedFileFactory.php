<?php

namespace Lumi\LumiPHP\Factory;

use Lumi\LumiPHP\Http\UploadedFile;

class UploadedFileFactory
{
    public static function fromGlobals(): array
    {
        $files = [];
        foreach ($_FILES as $key => $value) {
            $file = [];

            if (is_array($value['name'])) {
                $count = count($value['name']);

                for ($i = 0; $i < $count; $i++) {
                    $file[] = new UploadedFile(
                        name: $value['name'][$i],
                        tmpName: $value['tmp_name'][$i],
                        type: $value['type'][$i],
                        size: $value['size'][$i],
                        err: $value['error'][$i]
                    );
                }
            } else {
                $file = new UploadedFile(
                    name: $value['name'],
                    tmpName: $value['tmp_name'],
                    type: $value['type'],
                    size: $value['size'],
                    err: $value['error']
                );
            }

            $files[$key] = $file;
        }

        return $files;
    }
}
