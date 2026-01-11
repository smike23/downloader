<?php
/**
 * Zentrale Konfiguration
 * Wird von allen PHP-Dateien eingebunden
 */

/* ---------- Basis-Pfade ---------- */
define('BASE_PATH', __DIR__);

define('JOBS_DIR', BASE_PATH . '/jobs');
define('DOWNLOADS_DIR', BASE_PATH . '/downloads');
define('LOGS_DIR', BASE_PATH . '/logs');

/* ---------- Basis-URL ----------
 * inkl. Unterordner, in dem das Projekt liegt
 * Beispiel: https://example.com/tools/downloader
 */
define('BASE_URL', 'https://files.vpa4u.at/download'); // ANPASSEN

/* ---------- Cleanup-Konfiguration ---------- */
define('MAX_JOB_AGE', 24 * 3600);      // 24 Stunden
define('MAX_DOWNLOAD_AGE', 7 * 86400); // 7 Tage

/* ---------- Sonstiges ---------- */
define('JSON_FLAGS', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

/* ---------- Verzeichnisse sicherstellen ---------- */
@mkdir(JOBS_DIR, 0755, true);
@mkdir(DOWNLOADS_DIR, 0755, true);
@mkdir(LOGS_DIR, 0755, true);
