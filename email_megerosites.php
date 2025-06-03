<?php
require_once 'config.php';

$token = $_GET['token'] ?? '';
$success = false;
$error = '';

if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT * FROM email_megerosites WHERE token = :token");
    $stmt->execute([':token' => $token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $email = $row['email'];
        $lejart = strtotime($row['lejarati_ido']) < time();

        if ($lejart) {
            $error = "Ez a megerősítő link lejárt. Kérj új jelszót vagy regisztrálj újra.";
        } else {
            // Email aktiválás
            $update = $pdo->prepare("UPDATE vendeg_felhasznalok SET email_megerositve = 1 WHERE email = :email");
            $update->execute([':email' => $email]);

            // Token törlése
            $delete = $pdo->prepare("DELETE FROM email_megerosites WHERE token = :token");
            $delete->execute([':token' => $token]);

            $success = true;
        }
    } else {
        $error = "Érvénytelen vagy már felhasznált megerősítő link.";
    }
} else {
    $error = "Hiányzó token.";
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>E-mail megerősítés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px; text-align: center; }
        .success { color: green; font-size: 1.2em; }
        .error { color: red; font-size: 1.2em; }
        a { display: block; margin-top: 20px; color: #002d5a; text-decoration: none; }
    </style>
</head>
<body>
    <h2>E-mail megerősítés</h2>

    <?php if ($success): ?>
        <p class="success">Sikeresen megerősítetted az e-mail címed! Most már be tudsz jelentkezni.</p>
    <?php else: ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <a href="index.php">← Vissza a főoldalra</a>
</body>
</html>