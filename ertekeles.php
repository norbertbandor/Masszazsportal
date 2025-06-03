<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['felhasznalo_tipus'] !== 'vendeg') {
    header("Location: belepes.php");
    exit;
}

$vendeg_id = $_SESSION['felhasznalo_id'];
$szakember_id = $_GET['id'] ?? null;
$hibak = [];
$siker = false;

// Szakember adatainak lekérése
if (!$szakember_id || !is_numeric($szakember_id)) {
    die("Érvénytelen szakember ID.");
}
$stmt = $pdo->prepare("SELECT * FROM szakemberek WHERE id = :id AND visszaigazolt = 1");
$stmt->execute([':id' => $szakember_id]);
$szakember = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$szakember) {
    die("Nem található a szakember vagy nincs jóváhagyva.");
}

// Ellenőrizni, hogy már értékelt-e
$stmt = $pdo->prepare("SELECT * FROM ertekelesek WHERE vendeg_id = :vid AND szakember_id = :sid");
$stmt->execute([':vid' => $vendeg_id, ':sid' => $szakember_id]);
$volt = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$volt) {
    $pont = (int)$_POST['pont'] ?? 0;
    $velemeny = trim($_POST['velemeny'] ?? '');

    if ($pont < 1 || $pont > 5) {
        $hibak[] = "Az értékelésnek 1 és 5 között kell lennie.";
    }
    if (strlen($velemeny) > 250) {
        $hibak[] = "A vélemény maximum 250 karakter lehet.";
    }

    if (empty($hibak)) {
        $stmt = $pdo->prepare("INSERT INTO ertekelesek (vendeg_id, szakember_id, ertekeles, velemeny) VALUES (:vid, :sid, :pont, :velemeny)");
        $stmt->execute([
            ':vid' => $vendeg_id,
            ':sid' => $szakember_id,
            ':pont' => $pont,
            ':velemeny' => $velemeny
        ]);
        $siker = true;
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Értékelés leadása</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; max-width: 600px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: #002d5a; color: white; border: none; cursor: pointer; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h2><?= htmlspecialchars($szakember['nev']) ?> értékelése</h2>

    <?php if ($siker): ?>
        <p class="success">Köszönjük! Az értékelésed mentésre került.</p>
    <?php elseif ($volt): ?>
        <p class="success">Már leadtál értékelést erre a szakemberre.</p>
    <?php else: ?>
        <?php if (!empty($hibak)): ?>
            <ul class="error">
                <?php foreach ($hibak as $hiba): ?>
                    <li><?= htmlspecialchars($hiba) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post">
            <label>Értékelés (1–5):</label>
            <select name="pont" required>
                <option value="">-- Válassz --</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?> csillag</option>
                <?php endfor; ?>
            </select>

            <label>Vélemény (opcionális, max. 250 karakter):</label>
            <textarea name="velemeny" maxlength="250"></textarea>

            <button type="submit">Értékelés elküldése</button>
        </form>
    <?php endif; ?>
</body>
</html>