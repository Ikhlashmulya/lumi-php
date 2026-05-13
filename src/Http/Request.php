<?php

namespace Lumi\LumiPHP\Http;

use Lumi\LumiPHP\Helper\PathUtil;

class Request
{
    public string $uri;
    private array $queries;
    private array $headers;
    private string $rawBody;
    private array $parseBody;
    private string $path;
    private array $matches;
    public string $method;

    public function __construct(
        string $path = '', 
        array $matches = [],
        string $method = '',
        string $uri = '',
        array $queries = [],
        array $headers = [],
        string $rawBody = '',
        array $parseBody = []
    ) {
        $this->path = $path;
        $this->matches = $matches;
        $this->method = $method;
        $this->uri = $uri;
        $this->queries = $queries;
        $this->headers = $headers;
        $this->rawBody = $rawBody;
        $this->parseBody = $parseBody;
    }

    public function withRoute(string $path, array $matches): self
    {
        $this->path = $path;
        $this->matches = $matches;
        return $this;
    }

    public function param(string $key = ''): string|array|null
    {
        $paramNames = PathUtil::getParamNames($this->path);
        $params = array_combine($paramNames, $this->matches);

        return $key === '' ? $params : (isset($params[$key]) ? $params[$key] : null);
    }

    public function query(string $key): mixed
    {
        return isset($this->queries[$key]) ? $this->queries[$key] : null;
    }

    public function header(string $key): string|null
    {
        return isset($this->headers[$key]) ? $this->headers[$key] : null;
    }

    public function body(): array
    {
        return $this->parseBody;
    }

    public function json(): array
    {
        return json_decode($this->rawBody, true);
    }
}