<?php
require_once 'config.php';

$token = $_GET['token'] ?? '';
$errors = [];
$success = false;
$email = '';

if ($token) {
    $stmt = $pdo->prepare("SELECT * FROM jelszo_visszaallitas WHERE token = :token");
    $stmt->execute([':token' => $token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if (strtotime($row['lejarati_ido']) < time()) {
            $errors[] = "Ez a jelszó visszaállító link lejárt.";
        } else {
            $email = $row['email'];
        }
    } else {
        $errors[] = "Érvénytelen vagy már felhasznált link.";
    }
} else {
    $errors[] = "Hiányzó token.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {
    $uj1 = $_POST['uj1'] ?? '';
    $uj2 = $_POST['uj2'] ?? '';
    $tipus = $_POST['felhasznalo_tipus'] ?? '';

    if (empty($uj1) || empty($uj2) || empty($tipus)) {
        $errors[] = "Minden mező kitöltése kötelező.";
    } elseif ($uj1 !== $uj2) {
        $errors[] = "Az új jelszavak nem egyeznek.";
    } elseif (strlen($uj1) < 6) {
        $errors[] = "Az új jelszónak legalább 6 karakteresnek kell lennie.";
    }

    if (empty($errors)) {
        $hash = password_hash($uj1, PASSWORD_DEFAULT);

        if ($tipus === 'vendeg') {
            $stmt = $pdo->prepare("UPDATE vendeg_felhasznalok SET jelszo = :jelszo WHERE email = :email");
        } elseif ($tipus === 'szakember') {
            $stmt = $pdo->prepare("UPDATE szakemberek SET jelszo = :jelszo WHERE email = :email");
        } else {
            $errors[] = "Ismeretlen felhasználó típus.";
        }

        if (empty($errors)) {
            $stmt->execute([
                ':jelszo' => $hash,
                ':email' => $email
            ]);

            $del = $pdo->prepare("DELETE FROM jelszo_visszaallitas WHERE token = :token");
            $del->execute([':token' => $token]);

            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Új jelszó megadása</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: #002d5a; color: white; border: none; cursor: pointer; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Új jelszó beállítása</h2>

    <?php if ($success): ?>
        <p class="success">Sikeresen beállítottad az új jelszavad. Most már be tudsz jelentkezni.</p>
    <?php elseif (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if (!$success && $email): ?>
        <form method="post">
            <input type="hidden" name="felhasznalo_tipus" value="<?= isset($_POST['felhasznalo_tipus']) ? htmlspecialchars($_POST['felhasznalo_tipus']) : '' ?>">
            <label>Új jelszó:</label>
            <input type="password" name="uj1" required>

            <label>Új jelszó újra:</label>
            <input type="password" name="uj2" required>

            <label>Felhasználó típusa:</label>
            <select name="felhasznalo_tipus" required>
                <option value="">-- Válassz --</option>
                <option value="vendeg">Vendég</option>
                <option value="szakember">Szakember</option>
            </select>

            <button type="submit">Jelszó beállítása</button>
        </form>
    <?php endif; ?>
</body>
</html>