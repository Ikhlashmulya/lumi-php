<?php

namespace Lumi\LumiPHP;

class RouterGroup 
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
}