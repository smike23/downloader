<?php
if (php_sapi_name() !== 'cli') exit;

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/logger.php';

$jobId = $argv[1] ?? null;
$url   = $argv[2] ?? null;
if (!$jobId || !$url) exit;

$statusFile = JOBS_DIR . "/$jobId.json";
$d = json_decode(file_get_contents($statusFile), true);

$targetFile = DOWNLOADS_DIR . '/' . $d['filename'];
$fp = fopen($targetFile, 'w');

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_FILE => $fp,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_NOPROGRESS => false,
    CURLOPT_PROGRESSFUNCTION => function ($res, $total, $now) use ($statusFile) {

        $data = json_decode(file_get_contents($statusFile), true);
        if (!empty($data['cancel'])) return 1;

        if ($now > ($data['downloaded'] ?? 0)) {
            $data['downloaded'] = (int)$now;
        }

        if ($total > 0) {
            $data['total'] = (int)$total;
        }

        $tmp = $statusFile . '.tmp';
        file_put_contents($tmp, json_encode($data, JSON_FLAGS));
        rename($tmp, $statusFile);

        return 0;
    }
]);

curl_exec($ch);
curl_close($ch);
fclose($fp);

$d = json_decode(file_get_contents($statusFile), true);

if (!empty($d['cancel'])) {
    @unlink($targetFile);
    $d['status'] = 'abgebrochen';
    logEvent('warning', 'Download abgebrochen', ['jobId' => $jobId]);
} else {
    $d['status'] = 'fertig';
    $d['download_url'] = BASE_URL . '/downloads/' . rawurlencode($d['filename']);
    logEvent('info', 'Download abgeschlossen', [
        'jobId' => $jobId,
        'file' => $d['filename']
    ]);
}

$tmp = $statusFile . '.tmp';
file_put_contents($tmp, json_encode($d, JSON_FLAGS));
rename($tmp, $statusFile);
