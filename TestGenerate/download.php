<?php

define('TOEGANGSCODE', '');
define('PROJECT_DIR', __DIR__ . '/project');
define('LOGIN_PHP_RELATIVE', 'login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Alleen POST toegestaan.');
}

// CSRF-token valideren
session_start();
$csrfToken = $_POST['csrf_token'] ?? '';
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    header('Location: index.php?fout=csrf');
    exit;
}

$naam          = trim($_POST['naam']          ?? '');
$studentnummer = trim($_POST['studentnummer'] ?? '');
$code          = trim($_POST['code']          ?? '');

if ($naam === '' || $studentnummer === '' || $code === '') {
    header('Location: index.php?fout=leeg');
    exit;
}

// Lengtelimiet op naam
if (strlen($naam) > 100) {
    header('Location: index.php?fout=naam');
    exit;
}

if (!hash_equals(TOEGANGSCODE, $code)) {
    sleep(1);
    header('Location: index.php?fout=code');
    exit;
}

// Alleen cijfers toegestaan (consistent met client-side pattern)
if (!preg_match('/^[0-9]+$/', $studentnummer)) {
    header('Location: index.php?fout=nummer');
    exit;
}

if (!is_dir(PROJECT_DIR)) {
    http_response_code(500);
    exit('Projectmap niet gevonden. Zorg dat de map "project/" naast download.php staat.');
}

$loginPhpPath = PROJECT_DIR . '/' . LOGIN_PHP_RELATIVE;
if (!file_exists($loginPhpPath)) {
    http_response_code(500);
    exit('login.php niet gevonden in de projectmap.');
}

$hashInput = strtolower($naam) . '|' . strtolower($studentnummer);
$hash      = hash('sha256', $hashInput);

$tempBase = sys_get_temp_dir();
$tempDir  = $tempBase . '/project_' . bin2hex(random_bytes(8));

function copyDir(string $src, string $dst): void
{
    if (!mkdir($dst, 0700, true) && !is_dir($dst)) {
        throw new RuntimeException("Kan map niet aanmaken: $dst");
    }
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iter as $item) {
        $target = $dst . DIRECTORY_SEPARATOR . $iter->getSubPathname();
        if ($item->isDir()) {
            if (!mkdir($target, 0700, true) && !is_dir($target)) {
                throw new RuntimeException("Kan submap niet aanmaken: $target");
            }
        } else {
            copy($item->getPathname(), $target);
        }
    }
}

try {
    copyDir(PROJECT_DIR, $tempDir);
} catch (RuntimeException $e) {
    http_response_code(500);
    exit('Fout bij kopiëren van project: ' . $e->getMessage());
}

$tempLoginPhp = $tempDir . '/' . LOGIN_PHP_RELATIVE;
$loginContent = file_get_contents($tempLoginPhp);

if ($loginContent === false) {
    http_response_code(500);
    exit('Kan login.php niet lezen.');
}

$loginContent = preg_replace(
    '/(\$db_user\s*=\s*)"";/',
    '$1"' . addslashes($studentnummer) . '";',
    $loginContent
);
$loginContent = preg_replace(
    '/(\$db_name\s*=\s*)"";/',
    '$1"' . addslashes($studentnummer) . '";',
    $loginContent
);

$hashComment = "\n// Dit laten staan anders krijgt je een onvoldoende!: $hash\n";
$loginContent .= $hashComment;

file_put_contents($tempLoginPhp, $loginContent);

$zipFile = $tempBase . '/project_' . bin2hex(random_bytes(8)) . '.zip';

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    http_response_code(500);
    exit('Kan zip-archief niet aanmaken.');
}

$iter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($tempDir, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($iter as $file) {
    if (!$file->isFile()) {
        continue;
    }
    $filePath     = $file->getRealPath();
    $relativePath = substr($filePath, strlen($tempDir) + 1);
    $zip->addFile($filePath, 'project/' . $relativePath);
}

$zip->close();

function removeDir(string $dir): void
{
    if (!is_dir($dir)) return;
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iter as $item) {
        $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
    }
    rmdir($dir);
}

removeDir($tempDir);

$safeStudentnummer = preg_replace('/[^0-9]/', '', $studentnummer);
$downloadFilename  = 'project_' . $safeStudentnummer . '.zip';

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
header('Content-Length: ' . filesize($zipFile));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

readfile($zipFile);

unlink($zipFile);
exit;
