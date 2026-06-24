<?php

define('UPLOAD_DIR', '/var/www/uploads/');
define('MAX_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_EXT', ['php', 'html', 'css', 'js', 'env']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Methode niet toegestaan.');
}

// CSRF-token valideren
session_start();
$csrfToken = $_POST['csrf_token'] ?? '';
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    http_response_code(403);
    exit('Ongeldig verzoek. Ga terug en probeer opnieuw.');
}

// Uploadmap aanmaken indien nodig
if (!is_dir(UPLOAD_DIR)) {
    if (!mkdir(UPLOAD_DIR, 0755, true)) {
        http_response_code(500);
        exit('Kon uploadmap niet aanmaken.');
    }
}

// Altijd .htaccess bescherming afdwingen
$htaccess = UPLOAD_DIR . '.htaccess';
if (!file_exists($htaccess)) {
    @file_put_contents($htaccess, "Require all denied\nphp_flag engine off\nOptions -ExecCGI\n");
}

if (!is_writable(UPLOAD_DIR)) {
    http_response_code(500);
    exit('Uploadmap is niet schrijfbaar.');
}

// Formuliergegevens
$naam          = trim(strip_tags($_POST['naam']          ?? ''));
$studentnummer = trim(strip_tags($_POST['studentnummer'] ?? ''));
$klas          = trim(strip_tags($_POST['klas']          ?? ''));

if ($naam === '' || $studentnummer === '' || $klas === '') {
    http_response_code(400);
    exit('Vul alle verplichte velden in.');
}

if (strlen($naam) > 100) {
    http_response_code(400);
    exit('Naam mag maximaal 100 tekens bevatten.');
}

if (strlen($klas) > 20) {
    http_response_code(400);
    exit('Klas mag maximaal 20 tekens bevatten.');
}

if (!preg_match('/^[0-9]{6,10}$/', $studentnummer)) {
    http_response_code(400);
    exit('Ongeldig studentnummer (alleen cijfers, 6–10 tekens).');
}

if (!isset($_FILES['bestanden'])) {
    http_response_code(400);
    exit('Geen bestanden ontvangen.');
}

// Normaliseren naar arrays
$names      = $_FILES['bestanden']['name'];
$tmpNames   = $_FILES['bestanden']['tmp_name'];
$sizes      = $_FILES['bestanden']['size'];
$errorCodes = $_FILES['bestanden']['error'];

if (!is_array($names)) {
    $names      = [$names];
    $tmpNames   = [$tmpNames];
    $sizes      = [$sizes];
    $errorCodes = [$errorCodes];
}

if (empty($names[0])) {
    http_response_code(400);
    exit('Geen bestanden geselecteerd.');
}

// Submap aanmaken
$timestamp      = date('Ymd_His');
$veiligeStudent = preg_replace('/[^a-zA-Z0-9]/', '_', $studentnummer);
$subdir         = UPLOAD_DIR . $veiligeStudent . '_' . $timestamp . '/';

if (!mkdir($subdir, 0755, true) && !is_dir($subdir)) {
    http_response_code(500);
    exit('Kon submap niet aanmaken.');
}

@file_put_contents($subdir . '.htaccess', "Require all denied\nphp_flag engine off\nOptions -ExecCGI\n");

$errors    = [];
$opgeslagen = [];

for ($i = 0; $i < count($names); $i++) {
    $origNaam = $names[$i];

    if ($origNaam === '') {
        continue;
    }

    if ($errorCodes[$i] !== UPLOAD_ERR_OK) {
        $errors[] = "$origNaam gaf een uploadfout (code {$errorCodes[$i]}).";
        continue;
    }

    $tmpPath  = $tmpNames[$i];
    $fileSize = $sizes[$i];

    if (!is_uploaded_file($tmpPath)) {
        $errors[] = "$origNaam is geen geldig uploadbestand.";
        continue;
    }

    if ($fileSize > MAX_SIZE) {
        $errors[] = "$origNaam is te groot. Maximum is 10 MB.";
        continue;
    }

    // Extensie bepalen, ook voor .env
    $ext = strtolower(pathinfo($origNaam, PATHINFO_EXTENSION));
    if ($ext === '' && strtolower($origNaam) === '.env') {
        $ext = 'env';
    }

    if (!in_array($ext, ALLOWED_EXT, true)) {
        $errors[] = "$origNaam heeft een niet toegestaan bestandstype.";
        continue;
    }

    // Veilige bestandsnaam
    if (strtolower($origNaam) === '.env') {
        $veiligNaam = '.env';
    } else {
        $veiligNaam = preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($origNaam));
        $veiligNaam = preg_replace('/\.{2,}/', '.', $veiligNaam);
    }

    // Dubbele namen voorkomen
    $doelPad = $subdir . $veiligNaam;
    $teller  = 1;

    if (strtolower($veiligNaam) !== '.env') {
        $bestandsNaamZonderExt = pathinfo($veiligNaam, PATHINFO_FILENAME);
        $bestandExtensie       = pathinfo($veiligNaam, PATHINFO_EXTENSION);

        while (file_exists($doelPad)) {
            $nieuweNaam = $bestandsNaamZonderExt . '_' . $teller . '.' . $bestandExtensie;
            $doelPad    = $subdir . $nieuweNaam;
            $veiligNaam = $nieuweNaam;
            $teller++;
        }
    } else {
        while (file_exists($doelPad)) {
            $doelPad    = $subdir . '.env_' . $teller;
            $veiligNaam = '.env_' . $teller;
            $teller++;
        }
    }

    if (move_uploaded_file($tmpPath, $doelPad)) {
        $opgeslagen[] = $veiligNaam;
    } else {
        $errors[] = "$origNaam kon niet worden opgeslagen.";
    }
}

// Logging
$logRegel = implode("\t", [
    date('Y-m-d H:i:s'),
    $naam,
    $studentnummer,
    $klas,
    implode(', ', $opgeslagen),
    empty($errors) ? 'OK' : implode(' | ', $errors),
]) . PHP_EOL;

file_put_contents(UPLOAD_DIR . 'upload_log.txt', $logRegel, FILE_APPEND | LOCK_EX);

// Resultaat
if (!empty($opgeslagen) && empty($errors)) {
    $msg = urlencode('Bedankt ' . $naam . '! ' . count($opgeslagen) . ' bestand(en) succesvol ontvangen.');
    header("Location: index.php?status=ok&msg=$msg");
    exit;
}

if (!empty($opgeslagen) && !empty($errors)) {
    $msg = urlencode(count($opgeslagen) . ' bestand(en) opgeslagen. Fouten: ' . implode(' ', $errors));
    header("Location: index.php?status=partial&msg=$msg");
    exit;
}

http_response_code(400);
echo 'Upload mislukt:<br>' . implode('<br>', array_map('htmlspecialchars', $errors));
