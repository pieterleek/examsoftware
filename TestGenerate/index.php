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
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
        }

        h1 {
            font-size: 1.4rem;
            color: #1a1a2e;
            margin-bottom: 6px;
        }

        p.subtitle {
            font-size: 0.88rem;
            color: #666;
            margin-bottom: 28px;
        }

        label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #d0d5dd;
            border-radius: 6px;
            font-size: 0.95rem;
            color: #333;
            transition: border-color 0.2s;
            margin-bottom: 20px;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4a6cf7;
            box-shadow: 0 0 0 3px rgba(74,108,247,0.12);
        }

        .divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 4px 0 20px 0;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #4a6cf7;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        button[type="submit"]:hover {
            background: #3a57d7;
        }

        .error {
            background: #fff0f0;
            border-left: 3px solid #e74c3c;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 0.85rem;
            color: #c0392b;
            margin-bottom: 18px;
        }

        .notice {
            background: #fff5f5;
            border: 2px solid #e74c3c;
            border-radius: 6px;
            padding: 12px 16px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #c0392b;
            margin-bottom: 24px;
            text-align: center;
        }
    </style>
</head>
<body>


    <div class="card">
        <img src="talland.jpg" alt="Talland" style="width: 100%; max-width: 420px; margin-bottom: 20px;">
        <h1>Toets security SD2A</h1>
        <p class="subtitle">Vul je gegevens in om het startproject te downloaden.</p>

        <div class="notice">
            De opdracht voor de toets staat in het startproject als <u>opdracht.pdf</u>
        </div>

        <?php if (!empty($_GET['fout'])): ?>
            <div class="error">
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

        <form method="POST" action="download.php" id="downloadForm">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

            <label for="code">Toegangscode</label>
            <input
                type="password"
                id="code"
                name="code"
                placeholder="Deze krijg je van de docent"
                required
                autocomplete="off"
            >

            <hr class="divider">

            <label for="naam">Volledige naam</label>
            <input
                type="text"
                id="naam"
                name="naam"
                placeholder="bijv. Sinterklaas Pieterson"
                required
                autocomplete="name"
            >

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

            <button type="submit">⬇ Download startproject</button>
        </form>
    </div>
</body>
</html>
