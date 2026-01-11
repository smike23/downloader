<?php
require_once __DIR__ . '/config.php';

$job = $_GET['job'] ?? '';
$file = JOBS_DIR . "/$job.json";
if (!file_exists($file)) exit;

$d = json_decode(file_get_contents($file), true);
$d['cancel'] = true;
file_put_contents($file, json_encode($d, JSON_FLAGS));
