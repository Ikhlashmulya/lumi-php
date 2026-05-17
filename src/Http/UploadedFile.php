<?php

namespace Lumi\LumiPHP\Http;

class UploadedFile
{
    private string $name;
    private string $tmpName;
    private string $type;
    private int $size;
    private int $error;

    public function __construct(
        string $name = '',
        string $tmpName = '',
        string $type = '',
        int $size = 0,
        int $err = 0
    ) {
        $this->name = $name;
        $this->tmpName = $tmpName;
        $this->type = $type;
        $this->size = $size;
        $this->error = $err;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function isValid(): bool
    {
        return $this->error === 0;
    }

    public function moveTo(string $path): bool
    {
        return move_uploaded_file($this->tmpName, $path);
    }

    public function store(string $dir, string $newName = ''): bool {
        if (!is_dir($dir)) {
            throw new \RuntimeException('Directory does not exist');
        }

        $extention = strtolower(pathinfo($this->name, PATHINFO_EXTENSION));
        $path = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . ($newName === '' ? basename($this->name) : $newName . '.' . $extention);

        return $this->moveTo($path);
    }

}
