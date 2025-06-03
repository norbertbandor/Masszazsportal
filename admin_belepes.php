<?php
require_once 'config.php';

session_start();
$hiba = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $felhasznalonev = $_POST['felhasznalonev'] ?? '';
    $jelszo = $_POST['jelszo'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM adminok WHERE felhasznalonev = ?");
    $stmt->execute([$felhasznalonev]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($jelszo, $admin['jelszo'])) {
        $_SESSION['admin_belepve'] = true;
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $hiba = 'Hibás felhasználónév vagy jelszó.';
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin belépés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .doboz { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 400px; width: 100%; }
        h2 { text-align: center; color: #002d5a; }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #002d5a; color: white; border: none; border-radius: 5px; font-weight: bold; }
        .hiba { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="doboz">
        <h2>🔒 Admin belépés</h2>
        <?php if ($hiba): ?>
            <div class="hiba"><?= htmlspecialchars($hiba) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="felhasznalonev" placeholder="Felhasználónév" required>
            <input type="password" name="jelszo" placeholder="Jelszó" required>
            <button type="submit">Belépés</button>
        </form>
    </div>
</body>
</html>