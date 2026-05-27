<?php

namespace Lumi\LumiPHP\Debug;

class DebugErrorHandler {
    public static function handle(\Throwable $exception) 
    {
        if (ob_get_length()) ob_end_clean();

        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTrace();

        $codeSnippet = self::getCodeSnippet($file, $line);

        self::renderHtml($message, $file, $line, $codeSnippet, $trace);
        exit;
    }

    private static function getCodeSnippet(string $file, int $line, int $radius = 5): array 
    {
        if (!file_exists($file)) return [];
        
        $lines = file($file);
        $start = max(0, $line - $radius - 1);
        $end = min(count($lines), $line + $radius);

        $snippet = [];
        for ($i = $start; $i < $end; $i++) {
            $snippet[$i + 1] = htmlspecialchars($lines[$i]);
        }
        return $snippet;
    }

    private static function renderHtml(string $message, string $file, string $line, array $codeSnippet, array $trace) 
    {
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Error: <?= htmlspecialchars($message) ?></title>
            <style>
                body { font-family: 'Nunito', sans-serif; background: #f7fafc; color: #2d3748; margin: 0; padding: 40px; }
                .container { max-width: 1200px; margin: 0 auto; }
                .header { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); border-left: 6px solid #e53e3e; margin-bottom: 20px; }
                .error-class { color: #e53e3e; font-size: 14px; font-weight: bold; text-transform: uppercase; }
                .error-msg { font-size: 24px; margin: 10px 0; font-weight: 600; }
                .error-file { color: #718096; font-size: 14px; }
                .card { background: #fff; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 20px; }
                pre { background: #1a202c; color: #a0aec0; padding: 15px; border-radius: 6px; overflow-x: auto; font-family: 'Fira Code', monospace; }
                .line { display: block; }
                .highlight { background: #38a169; color: #fff; display: block; font-weight: bold; padding: 2px 5px; border-radius: 3px; }
                .trace-item { padding: 10px 0; border-bottom: 1px solid #edf2f7; font-size: 14px; }
                .trace-item:last-child { border-bottom: none; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <div class="error-class">Unhandled Exception</div>
                    <div class="error-msg"><?= htmlspecialchars($message) ?></div>
                    <div class="error-file">Di <strong><?= $file ?></strong> pada baris <strong><?= $line ?></strong></div>
                </div>

                <div class="card">
                    <h3>Source Code</h3>
                    <pre><code><?php
                    foreach ($codeSnippet as $currentLine => $code) {
                        if ($currentLine == $line) {
                            echo "<span class='highlight'>$currentLine: $code</span>";
                        } else {
                            echo "<span class='line'>$currentLine: $code</span>";
                        }
                    }
                    ?></code></pre>
                </div>

                <div class="card">
                    <h3>Stack Trace</h3>
                    <?php foreach ($trace as $index => $t): ?>
                        <div class="trace-item">
                            <strong>#<?= $index ?></strong> 
                            <?= isset($t['class']) ? $t['class'] . $t['type'] : '' ?><?= $t['function'] ?>()
                            <br>
                            <span style="color: #718096; font-size: 12px;">
                                <?= isset($t['file']) ? $t['file'] . ':' . $t['line'] : '[internal function]' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
