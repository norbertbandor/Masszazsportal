<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['admin_belepve']) || $_SESSION['admin_belepve'] !== true) {
    header('Location: admin_belepes.php');
    exit;
}

$uzenet = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cim = trim($_POST['cim'] ?? '');
    $tartalom = trim($_POST['tartalom'] ?? '');
    $hashtag = trim($_POST['hashtag'] ?? '');

    if ($cim && $tartalom) {
        $stmt = $pdo->prepare("INSERT INTO blog (cim, tartalom, hashtag) VALUES (?, ?, ?)");
        $stmt->execute([$cim, $tartalom, $hashtag]);
        $uzenet = "✅ A bejegyzés sikeresen elmentve.";
    } else {
        $uzenet = "❗ A cím és a tartalom megadása kötelező.";
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Új blogbejegyzés létrehozása</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; background-color: #f4f4f4; padding: 20px; }
        .doboz { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #002d5a; text-align: center; }
        input[type="text"], textarea {
            width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px;
        }
        textarea { height: 200px; resize: vertical; }
        button {
            width: 100%; padding: 12px; background-color: #002d5a; color: white; border: none;
            border-radius: 6px; font-size: 16px; cursor: pointer;
        }
        button:hover { background-color: #004080; }
        .uzenet { text-align: center; margin-top: 15px; font-weight: bold; }
    </style>
</head>
<body>
<div class="doboz">
    <h2>📝 Új blogbejegyzés</h2>

    <?php if ($uzenet): ?>
        <div class="uzenet"><?= htmlspecialchars($uzenet) ?></div>
    <?php endif; ?>

    <form method="post">
        <label for="cim">Cím:</label>
        <input type="text" name="cim" id="cim" required>

        <label for="tartalom">Tartalom:</label>
        <textarea name="tartalom" id="tartalom" required></textarea>

        <label for="hashtag">Hashtagek (vesszővel):</label>
        <input type="text" name="hashtag" id="hashtag" placeholder="#relaxáció, #svédmasszázs">

        <button type="submit">💾 Bejegyzés mentése</button>
    </form>
</div>
</body>
</html>