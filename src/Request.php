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

    public function param(string $key = ''): string|array|null
    {
        $paramNames = PathUtil::getParamNames($this->path);
        $params = array_combine($paramNames, $this->matches);

        return $key === '' ? $params : (isset($params[$key]) ? $params[$key] : null);
    }

    public function query(string $key): mixed
    {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : null;
    }

    public function header(string $key): string|null
    {
        $headers = getallheaders();
        return isset($headers[$key]) ? $headers[$key] : null;
    }

    public function body(): array
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $_POST;
        }

        [$post, $_] = request_parse_body();

        return $post;
    }

    public function json(): array
    {
        $json_data = file_get_contents('php://input');
        return json_decode($json_data, true);
    }
}