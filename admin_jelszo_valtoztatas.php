<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_belepes.php");
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $regi = $_POST['regi'] ?? '';
    $uj1 = $_POST['uj1'] ?? '';
    $uj2 = $_POST['uj2'] ?? '';

    if (empty($regi) || empty($uj1) || empty($uj2)) {
        $errors[] = "Minden mezőt ki kell tölteni.";
    } elseif ($uj1 !== $uj2) {
        $errors[] = "Az új jelszavak nem egyeznek.";
    } elseif (strlen($uj1) < 6) {
        $errors[] = "Az új jelszónak legalább 6 karakter hosszúnak kell lennie.";
    } else {
        $felhasznalonev = $_SESSION['admin_felhasznalo'];
        $stmt = $pdo->prepare("SELECT jelszo FROM adminok WHERE felhasznalonev = :felhasznalonev");
        $stmt->execute([':felhasznalonev' => $felhasznalonev]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($regi, $admin['jelszo'])) {
            $errors[] = "A jelenlegi jelszó nem megfelelő.";
        } else {
            $uj_hash = password_hash($uj1, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE adminok SET jelszo = :jelszo WHERE felhasznalonev = :felhasznalonev");
            $update->execute([
                ':jelszo' => $uj_hash,
                ':felhasznalonev' => $felhasznalonev
            ]);
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin jelszó módosítás</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; max-width: 500px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: #002d5a; color: white; border: none; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Admin jelszó módosítása</h2>

    <?php if ($success): ?>
        <p class="success">A jelszavad sikeresen módosítva lett.</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <label>Jelenlegi jelszó:</label>
        <input type="password" name="regi" required>

        <label>Új jelszó:</label>
        <input type="password" name="uj1" required>

        <label>Új jelszó újra:</label>
        <input type="password" name="uj2" required>

        <button type="submit">Jelszó módosítása</button>
    </form>
</body>
</html>