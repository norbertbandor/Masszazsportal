<?php
session_start();
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $jelszo = $_POST['jelszo'];
    $tipus = $_POST['felhasznalo_tipus']; // 'vendeg' vagy 'szakember'

    if (empty($email) || empty($jelszo) || empty($tipus)) {
        $errors[] = "Minden mező kitöltése kötelező.";
    }

    if (empty($errors)) {
        if ($tipus === 'vendeg') {
            $stmt = $pdo->prepare("SELECT * FROM vendeg_felhasznalok WHERE email = :email");
        } elseif ($tipus === 'szakember') {
            $stmt = $pdo->prepare("SELECT * FROM szakemberek WHERE email = :email");
        } else {
            $errors[] = "Érvénytelen felhasználó típus.";
        }

        if (empty($errors)) {
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($jelszo, $user['jelszo'])) {
                if ($tipus === 'vendeg' && $user['email_megerositve'] != 1) {
                    $errors[] = "Előbb erősítsd meg az e-mail címed.";
                } else {
                    $_SESSION['felhasznalo_id'] = $user['id'];
                    $_SESSION['felhasznalo_tipus'] = $tipus;
                    $_SESSION['felhasznalo_nev'] = $user['nev'];

                    header("Location: index.php");
                    exit;
                }
            } else {
                $errors[] = "Hibás e-mail cím vagy jelszó.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 15px; padding: 10px 20px; background: #002d5a; color: white; border: none; cursor: pointer; }
        .error { color: red; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Bejelentkezés</h2>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>E-mail cím:</label>
        <input type="email" name="email" required>

        <label>Jelszó:</label>
        <input type="password" name="jelszo" required>

        <label>Felhasználó típusa:</label>
        <select name="felhasznalo_tipus" required>
            <option value="">-- Válassz --</option>
            <option value="vendeg">Vendég</option>
            <option value="szakember">Szakember</option>
        </select>

        <button type="submit">Bejelentkezés</button>
    </form>
</body>
</html>