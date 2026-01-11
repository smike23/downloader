<?php
require_once __DIR__ . '/config.php';

$job = $_GET['job'] ?? '';
$file = JOBS_DIR . "/$job.json";
if (!file_exists($file)) exit;

$d = json_decode(file_get_contents($file), true);

$downloaded = (int)($d['downloaded'] ?? 0);
$total = (int)($d['total'] ?? 0);

$percent = null;
if ($total > 0 && $downloaded > 0) {
    $percent = min(100, round(($downloaded / $total) * 100));
}

echo json_encode([
    'status' => $d['status'],
    'percent' => $percent,
    'downloaded_mb' => round($downloaded / 1024 / 1024, 0),
    'total_mb' => $total > 0 ? round($total / 1024 / 1024, 2) : null,
    'filename' => $d['filename'],
    'download_url' => $d['download_url'] ?? null
], JSON_FLAGS);
