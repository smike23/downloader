<?php

$baseUrl = 'https://files.vpa4u.at/download'; // anpassen getCurrentBaseUrl() --> funktioniert nicht immer...;


$jobsDir = __DIR__ . '/jobs';
$downloadDirName = 'downloads';
$downloadDir = __DIR__ . '/' . $downloadDirName; // '/downloads';
$frontendUrl = $baseUrl .  '/frontend.html'; // getCurrentBaseUrl() .

function getCurrentDomain(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || ($_SERVER['SERVER_PORT'] == 443);

    $protocol = $https ? 'https' : 'http';

    return $protocol . '://' . $_SERVER['HTTP_HOST'];
}


function getCurrentBaseUrl(): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
          || ($_SERVER['SERVER_PORT'] == 443);

    $protocol = $https ? 'https' : 'http';

    $host = $_SERVER['HTTP_HOST'];

    // Pfad zum aktuellen Skript (ohne Dateiname)
    $path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

    return $protocol . '://' . $host . $path;
}
