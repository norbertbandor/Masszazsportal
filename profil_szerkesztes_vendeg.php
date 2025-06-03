<?php
require_once 'config.php';
include 'fejlec.php';

if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['felhasznalo_tipus'] !== 'vendeg') {
    header("Location: belepes.php");
    exit;
}

$id = $_SESSION['felhasznalo_id'];
$errors = [];
$success = false;

// Vendég adatainak lekérése
$stmt = $pdo->prepare("SELECT nev FROM vendeg_felhasznalok WHERE id = :id");
$stmt->execute([':id' => $id]);
$vendeg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vendeg) {
    die("Vendég nem található.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nev = trim($_POST['nev'] ?? '');
    $jelenlegi = $_POST['jelenlegi'] ?? '';
    $uj1 = $_POST['uj1'] ?? '';
    $uj2 = $_POST['uj2'] ?? '';

    if (empty($nev)) {
        $errors[] = "A név mező nem lehet üres.";
    }

    // Jelszó módosítás
    if (!empty($jelenlegi) || !empty($uj1) || !empty($uj2)) {
        if (empty($jelenlegi) || empty($uj1) || empty($uj2)) {
            $errors[] = "Ha jelszót szeretnél módosítani, minden jelszómezőt ki kell tölteni.";
        } elseif ($uj1 !== $uj2) {
            $errors[] = "Az új jelszavak nem egyeznek.";
        } elseif (strlen($uj1) < 6) {
            $errors[] = "Az új jelszónak legalább 6 karakteresnek kell lennie.";
        } else {
            // Jelenlegi jelszó ellenőrzése
            $stmt = $pdo->prepare("SELECT jelszo FROM vendeg_felhasznalok WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || !password_verify($jelenlegi, $row['jelszo'])) {
                $errors[] = "A jelenlegi jelszó nem megfelelő.";
            }
        }
    }

    if (empty($errors)) {
        if (!empty($uj1)) {
            $uj_hash = password_hash($uj1, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE vendeg_felhasznalok SET nev = :nev, jelszo = :jelszo WHERE id = :id");
            $stmt->execute([':nev' => $nev, ':jelszo' => $uj_hash, ':id' => $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE vendeg_felhasznalok SET nev = :nev WHERE id = :id");
            $stmt->execute([':nev' => $nev, ':id' => $id]);
        }
        $_SESSION['felhasznalo_nev'] = $nev;
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Adatok módosítása</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; max-width: 600px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: #002d5a; color: white; border: none; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Vendég adatainak módosítása</h2>

    <?php if ($success): ?>
        <p class="success">A módosítások sikeresen elmentve.</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <label>Teljes név:</label>
        <input type="text" name="nev" value="<?= htmlspecialchars($vendeg['nev']) ?>" required>

        <hr>
        <h3>Jelszó módosítása (opcionális)</h3>

        <label>Jelenlegi jelszó:</label>
        <input type="password" name="jelenlegi">

        <label>Új jelszó:</label>
        <input type="password" name="uj1">

        <label>Új jelszó újra:</label>
        <input type="password" name="uj2">

        <button type="submit">Mentés</button>
    </form>
</body>
</html>