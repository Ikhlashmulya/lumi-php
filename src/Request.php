<?php

namespace Lumi\LumiPHP;

class Request
{
    private string $path;
    private array $matches;

    public function __construct(string $path, array $matches)
    {
        $this->path = $path;
        $this->matches = $matches;
    }

    public function getParam(string $key): string
    {
        $paramNames = PathUtil::getParamNames($this->path);
        $params = array_combine($paramNames, $this->matches);

        return $params[$key];
    }
}