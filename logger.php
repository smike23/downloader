<?php
require_once __DIR__ . '/config.php';

function logEvent(string $level, string $message, array $context = []): void
{
    $entry = [
        'time' => date('Y-m-d H:i:s'),
        'level' => $level,
        'message' => $message,
        'context' => $context
    ];

    file_put_contents(
        LOGS_DIR . '/app.log',
        json_encode($entry, JSON_FLAGS) . PHP_EOL,
        FILE_APPEND
    );
}
