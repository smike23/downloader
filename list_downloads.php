<?php
require_once __DIR__ . '/config.php';

/* ---------- Datei löschen ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $file = basename($_POST['delete']);
    $path = DOWNLOADS_DIR . '/' . $file;

    if (is_file($path)) {
        unlink($path);
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

/* ---------- Dateien sammeln + Gesamtsumme ---------- */
$files = [];
$totalBytes = 0;

if (is_dir(DOWNLOADS_DIR)) {
    foreach (scandir(DOWNLOADS_DIR) as $file) {
        if ($file === '.' || $file === '..') continue;

        $path = DOWNLOADS_DIR . '/' . $file;
        if (!is_file($path)) continue;

        $sizeBytes = filesize($path);
        $totalBytes += $sizeBytes;

        $files[] = [
            'name' => $file,
            'size_mb' => number_format(
                $sizeBytes / 1024 / 1024,
                2,
                ',',
                '.'
            ),
            'date' => date('d.m.Y H:i', filemtime($path)),
            'url'  => BASE_URL . '/downloads/' . rawurlencode($file),
            'id'   => md5($file)
        ];
    }
}

/* ---------- Gesamtsumme formatiert ---------- */
$totalSizeFormatted = number_format(
    $totalBytes / 1024 / 1024,
    2,
    ',',
    '.'
);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Downloads verwalten</title>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 16px;
}

.container {
    max-width: 1100px;
    margin: 0 auto;
}

.header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 10px;
    align-items: center;
}

.header a {
    font-weight: bold;
    text-decoration: none;
}

.summary {
    margin-top: 10px;
    padding: 10px;
    background: #f7f7f7;
    border: 1px solid #ddd;
    font-weight: bold;
}

/* ---------- Desktop Tabelle ---------- */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
    vertical-align: top;
}

th {
    background: #f0f0f0;
}

td:first-child {
    word-break: break-all;
    overflow-wrap: anywhere;
}

.url-input {
    width: 100%;
    padding: 8px;
    font-size: 14px;
}

button {
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
    margin-bottom: 5px;
}

/* ---------- Mobile Cards ---------- */
.cards {
    display: none;
    margin-top: 20px;
}

.card {
    border: 1px solid #ddd;
    padding: 12px;
    margin-bottom: 12px;
    background: #fafafa;
}

.card strong {
    display: block;
    word-break: break-all;
    overflow-wrap: anywhere;
    margin-bottom: 6px;
}

.card .meta {
    font-size: 14px;
    margin-bottom: 8px;
}

.card input {
    width: 100%;
    margin-bottom: 8px;
    padding: 8px;
}

/* ---------- Responsive ---------- */
@media (max-width: 768px) {
    table {
        display: none;
    }

    .cards {
        display: block;
    }

    button {
        width: 100%;
    }
}
</style>
</head>

<body>

<div class="container">

    <div class="header">
        <h2>Gespeicherte Downloads</h2>
        <a href="<?= htmlspecialchars(BASE_URL) ?>">⬅ Zurück zum Download</a>
    </div>

    <div class="summary">
        Gesamtspeicherplatz: <?= $totalSizeFormatted ?> MB
        (<?= count($files) ?> Dateien)
    </div>

    <?php if (empty($files)): ?>
        <p>Keine Dateien vorhanden.</p>
    <?php else: ?>

    <!-- Desktop Tabelle -->
    <table>
        <tr>
            <th>Dateiname</th>
            <th>Größe</th>
            <th>Datum</th>
            <th>URL</th>
            <th>Aktionen</th>
        </tr>

        <?php foreach ($files as $f): ?>
        <tr>
            <td><?= htmlspecialchars($f['name']) ?></td>
            <td><?= $f['size_mb'] ?> MB</td>
            <td><?= $f['date'] ?></td>
            <td>
                <input type="text"
                       id="url_<?= $f['id'] ?>"
                       class="url-input"
                       value="<?= htmlspecialchars($f['url']) ?>"
                       readonly>
            </td>
            <td>
                <button onclick="copyUrl('<?= $f['id'] ?>')">URL kopieren</button>

                <form method="post"
                      onsubmit="return confirm('Datei wirklich löschen?');">
                    <input type="hidden" name="delete" value="<?= htmlspecialchars($f['name']) ?>">
                    <button type="submit">Löschen</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Mobile Cards -->
    <div class="cards">
        <?php foreach ($files as $f): ?>
        <div class="card">
            <strong><?= htmlspecialchars($f['name']) ?></strong>

            <div class="meta">
                Größe: <?= $f['size_mb'] ?> MB<br>
                Datum: <?= $f['date'] ?>
            </div>

            <input type="text"
                   id="url_m_<?= $f['id'] ?>"
                   value="<?= htmlspecialchars($f['url']) ?>"
                   readonly>

            <button onclick="copyUrlMobile('<?= $f['id'] ?>')">
                URL kopieren
            </button>

            <form method="post"
                  onsubmit="return confirm('Datei wirklich löschen?');">
                <input type="hidden" name="delete" value="<?= htmlspecialchars($f['name']) ?>">
                <button type="submit">Datei löschen</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>

</div>

<script>
function copyUrl(id) {
    const input = document.getElementById('url_' + id);
    input.select();
    document.execCommand('copy');
}

function copyUrlMobile(id) {
    const input = document.getElementById('url_m_' + id);
    input.select();
    document.execCommand('copy');
}
</script>

</body>
</html>
