<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['felhasznalo_id']) || !isset($_SESSION['felhasznalo_tipus'])) {
    header("Location: belepes.php");
    exit;
}

$felhasznalo_id = $_SESSION['felhasznalo_id'];
$felhasznalo_tipus = $_SESSION['felhasznalo_tipus'];

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jelenlegi = $_POST['jelenlegi'] ?? '';
    $uj1 = $_POST['uj1'] ?? '';
    $uj2 = $_POST['uj2'] ?? '';

    if (empty($jelenlegi) || empty($uj1) || empty($uj2)) {
        $errors[] = "Minden mező kitöltése kötelező.";
    }

    if ($uj1 !== $uj2) {
        $errors[] = "Az új jelszavak nem egyeznek.";
    }

    if (strlen($uj1) < 6) {
        $errors[] = "Az új jelszónak legalább 6 karakteresnek kell lennie.";
    }

    if (empty($errors)) {
        $tabla = $felhasznalo_tipus === 'vendeg' ? 'vendeg_felhasznalok' : 'szakemberek';

        $stmt = $pdo->prepare("SELECT jelszo FROM $tabla WHERE id = :id");
        $stmt->execute([':id' => $felhasznalo_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($jelenlegi, $row['jelszo'])) {
            $uj_hash = password_hash($uj1, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE $tabla SET jelszo = :jelszo WHERE id = :id");
            $update->execute([
                ':jelszo' => $uj_hash,
                ':id' => $felhasznalo_id
            ]);
            $success = true;
        } else {
            $errors[] = "A jelenlegi jelszó nem helyes.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Jelszó módosítása</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: #002d5a; color: white; border: none; cursor: pointer; }
        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Jelszó módosítása</h2>

    <?php if ($success): ?>
        <p class="success">Sikeresen megváltoztattad a jelszavad!</p>
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
        <input type="password" name="jelenlegi" required>

        <label>Új jelszó:</label>
        <input type="password" name="uj1" required>

        <label>Új jelszó újra:</label>
        <input type="password" name="uj2" required>

        <button type="submit">Jelszó módosítása</button>
    </form>
</body>
</html>