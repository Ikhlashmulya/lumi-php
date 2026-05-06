<?php

namespace Lumi\LumiPHP;

class RouterGroup implements RouterInterface
{
    private Application $application;
    private string $prefixPath;

    public function __construct(Application &$application, string $prefixPath, callable ...$handlers)
    {
        $this->application = $application;
        $this->prefixPath = $prefixPath;
        $this->application->use($prefixPath, ...$handlers);
    }

    public function get(string $path, callable ...$handler): void
    {
        $this->application->get($this->prefixPath . $path, ...$handler);
    }

    public function post(string $path, callable ...$handler): void
    {
        $this->application->post($this->prefixPath . $path, ...$handler);
    }

    public function put(string $path, callable ...$handler): void
    {
        $this->application->put($this->prefixPath . $path, ...$handler);
    }

    public function patch(string $path, callable ...$handler): void
    {
        $this->application->patch($this->prefixPath . $path, ...$handler);
    }

    public function delete(string $path, callable ...$handler): void
    {
        $this->application->delete($this->prefixPath . $path, ...$handler);
    }

    public function trace(string $path, callable ...$handler): void
    {
        $this->application->trace($this->prefixPath . $path, ...$handler);
    }

    public function options(string $path, callable ...$handler): void
    {
        $this->application->options($this->prefixPath . $path, ...$handler);
    }

    public function head(string $path, callable ...$handler): void
    {
        $this->application->head($this->prefixPath . $path, ...$handler);
    }
}