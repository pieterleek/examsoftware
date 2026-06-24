<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Downloaden</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f0f4ff;
            --surface: #ffffff;
            --surface2: #f7f9ff;
            --border: #d8e0f5;
            --text: #141c38;
            --text-soft: #6370a0;
            --accent: #3a56e8;
            --accent-hover: #2941cc;
            --accent-light: #eef1fd;
            --danger: #e84444;
            --danger-bg: #fff0f0;
            --warning-bg: #fff8e8;
            --warning-border: #f1d58a;
            --warning-text: #8d6700;
            --shadow: 0 8px 40px rgba(58,86,232,0.10);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: "DM Sans", sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 3rem 1rem 5rem;
            background-image:
                radial-gradient(circle at 0% 0%, #d6dffe 0%, transparent 45%),
                radial-gradient(circle at 100% 100%, #dff0e8 0%, transparent 40%);
        }

        header {
            width: min(520px, 100%);
            margin-bottom: 2.5rem;
            animation: slide-in 500ms ease-out;
        }

        .school-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: var(--accent-light);
            color: var(--accent);
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            margin-bottom: 1rem;
            border: 1px solid #c5d0fa;
        }

        header h1 {
            font-family: "Syne", sans-serif;
            font-size: clamp(1.9rem, 5vw, 2.6rem);
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        header h1 span { color: var(--accent); }

        header p {
            margin-top: 0.6rem;
            color: var(--text-soft);
            font-size: 1rem;
        }

        .card {
            width: min(520px, 100%);
            background: var(--surface);
            border-radius: 24px;
            box-shadow: var(--shadow);
            padding: 2.5rem;
            border: 1px solid var(--border);
            animation: slide-in 550ms ease-out;
        }

        .notice {
            background: var(--warning-bg);
            border: 1.5px solid var(--warning-border);
            border-radius: 12px;
            padding: 0.9rem 1.1rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--warning-text);
            margin-bottom: 1.75rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .section-label {
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--text-soft);
            margin-bottom: 1rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            margin-bottom: 1.1rem;
        }

        .field label {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--text);
        }

        .field input {
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            font: 500 0.95rem "DM Sans", sans-serif;
            color: var(--text);
            background: var(--surface2);
            transition: border-color 180ms, box-shadow 180ms;
        }

        .field input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(58,86,232,0.12);
            background: #fff;
        }

        .field input::placeholder { color: #aab2d0; }

        .divider {
            height: 1px;
            background: var(--border);
            margin: 0.25rem -2.5rem 1.5rem;
        }

        .error {
            background: var(--danger-bg);
            border: 1.5px solid #f4b3b3;
            border-radius: 12px;
            padding: 0.85rem 1.1rem;
            font-size: 0.88rem;
            color: #b13030;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit {
            width: 100%;
            border: 0;
            border-radius: 12px;
            padding: 0.9rem 2rem;
            background: var(--accent);
            color: #fff;
            font: 700 0.97rem "Syne", sans-serif;
            letter-spacing: 0.02em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: background 180ms, transform 180ms, box-shadow 180ms;
            box-shadow: 0 4px 16px rgba(58,86,232,0.25);
            margin-top: 0.5rem;
        }

        .btn-submit:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(58,86,232,0.32);
        }

        .btn-submit:active { transform: translateY(0); }

        @keyframes slide-in {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 560px) {
            .card { padding: 1.5rem; }
            .divider { margin: 0.25rem -1.5rem 1.5rem; }
        }
    </style>
</head>
<body>

<header>
    <div class="school-tag">
        <svg width="10" height="10" viewBox="0 0 10 10">
            <circle cx="5" cy="5" r="4" fill="currentColor"/>
        </svg>
        Toets SD2A
    </div>
    <h1>Download het <span>startproject</span></h1>
    <p>Vul je gegevens in om het startproject te downloaden.</p>
</header>

<div class="card">

    <div class="notice">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
            <circle cx="12" cy="12" r="10"/>
            <line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        De opdracht staat in het startproject als <u>opdracht.pdf</u>
    </div>

    <?php if (!empty($_GET['fout'])): ?>
        <div class="error">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0">
                <circle cx="12" cy="12" r="10"/>
                <line x1="15" y1="9" x2="9" y2="15"/>
                <line x1="9" y1="9" x2="15" y2="15"/>
            </svg>
            <?php
                $fouten = [
                    'code'   => 'Ongeldige toegangscode.',
                    'leeg'   => 'Vul alle velden in.',
                    'nummer' => 'Studentnummer mag alleen cijfers bevatten.',
                    'naam'   => 'Naam mag maximaal 100 tekens bevatten.',
                    'csrf'   => 'Ongeldig verzoek, probeer opnieuw.',
                ];
                echo htmlspecialchars($fouten[$_GET['fout']] ?? 'Onbekende fout.');
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="download.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

        <p class="section-label">Toegang</p>

        <div class="field">
            <label for="code">Toegangscode</label>
            <input
                type="password"
                id="code"
                name="code"
                placeholder="Deze krijg je van de docent"
                required
                autocomplete="off"
            >
        </div>

        <div class="divider"></div>

        <p class="section-label">Jouw gegevens</p>

        <div class="field">
            <label for="naam">Volledige naam</label>
            <input
                type="text"
                id="naam"
                name="naam"
                placeholder="bijv. Sinterklaas Pieterson"
                required
                autocomplete="name"
            >
        </div>

        <div class="field">
            <label for="studentnummer">Studentnummer</label>
            <input
                type="text"
                id="studentnummer"
                name="studentnummer"
                placeholder="bijv. 10104494"
                required
                autocomplete="off"
                pattern="[0-9]+"
                title="Alleen cijfers"
            >
        </div>

        <button type="submit" class="btn-submit">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Download startproject
        </button>
    </form>
</div>

</body>
</html>
