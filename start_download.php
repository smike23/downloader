<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logger.php';

$url = filter_input(INPUT_POST, 'url', FILTER_VALIDATE_URL);
if (!$url) {
    http_response_code(400);
    exit;
}

$jobId = uniqid('job_', true);

function getFilenameFromUrl(string $url): string
{
    $path = parse_url($url, PHP_URL_PATH);
    $name = basename($path) ?: 'download_' . date('Ymd_His');
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $name);
}

$filename = getFilenameFromUrl($url);
$statusFile = JOBS_DIR . "/$jobId.json";

file_put_contents($statusFile, json_encode([
    'status' => 'running',
    'filename' => $filename,
    'downloaded' => 0,
    'total' => 0,
    'cancel' => false
], JSON_FLAGS));

logEvent('info', 'Download gestartet', [
    'jobId' => $jobId,
    'url' => $url
]);

header('Content-Type: application/json');
echo json_encode(['jobId' => $jobId]);

$cmd = sprintf(
    'php %s %s %s > /dev/null 2>&1 &',
    escapeshellarg(BASE_PATH . '/worker.php'),
    escapeshellarg($jobId),
    escapeshellarg($url)
);
exec($cmd);
