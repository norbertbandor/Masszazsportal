<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'mailer_config.php';

require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nev = trim($_POST['nev']);
    $email = trim($_POST['email']);
    $jelszo = $_POST['jelszo'];

    if (empty($nev) || empty($email) || empty($jelszo)) {
        $errors[] = "Minden mező kitöltése kötelező.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Hibás e-mail formátum.";
    }

    if (strlen($jelszo) < 6) {
        $errors[] = "A jelszónak legalább 6 karakter hosszúnak kell lennie.";
    }

    // E-mail ellenőrzés
    $ellenorzes = $pdo->prepare("SELECT id FROM vendeg_felhasznalok WHERE email = ?");
    $ellenorzes->execute([$email]);
    if ($ellenorzes->fetch()) {
        $errors[] = "Ez az e-mail cím már regisztrálva van.";
    }

    if (empty($errors)) {
        $jelszo_hash = password_hash($jelszo, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO vendeg_felhasznalok (nev, email, jelszo) VALUES (?, ?, ?)");
        $stmt->execute([$nev, $email, $jelszo_hash]);

        // Token generálása és mentése
        $token = bin2hex(random_bytes(16));
        $lejar = date('Y-m-d H:i:s', time() + 3600);
        $stmt2 = $pdo->prepare("INSERT INTO email_megerosites (email, token, lejarati_ido) VALUES (?, ?, ?)");
        $stmt2->execute([$email, $token, $lejar]);

        // E-mail küldése
        $url = "https://masszazsportal.hu/email_megerosites.php?token=$token";
        $mail = new PHPMailer();
        $mail->setFrom('info@masszazsportal.hu', 'Masszázsportál');
        $mail->addAddress($email, $nev);
        $mail->Subject = "E-mail megerősítés";
        $mail->isHTML(true);
        $mail->Body = "Kérlek erősítsd meg az e-mail címed az alábbi linkre kattintva:<br><br>
                       <a href='$url'>$url</a><br><br>
                       A link 1 órán keresztül érvényes.";

        if ($mail->send()) {
            $success = true;
        } else {
            $errors[] = "Sikertelen e-mail küldés: " . $mail->ErrorInfo;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Vendég regisztráció</title>
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
    <h2>Vendég regisztráció</h2>

    <?php if ($success): ?>
        <p class="success">Sikeres regisztráció! Kérjük, erősítsd meg az e-mail címed a kiküldött levélben.</p>
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
        <input type="text" name="nev" required>

        <label>E-mail cím:</label>
        <input type="email" name="email" required>

        <label>Jelszó:</label>
        <input type="password" name="jelszo" required>

        <button type="submit">Regisztráció</button>
    </form>
</body>
</html>