<?php
require_once 'config.php';
require_once 'mailer_config.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $tipus = $_POST['felhasznalo_tipus'];

    if (empty($email) || empty($tipus)) {
        $errors[] = "Minden mezőt ki kell tölteni.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Hibás e-mail cím formátum.";
    }

    if (empty($errors)) {
        if ($tipus === 'vendeg') {
            $tabla = 'vendeg_felhasznalok';
        } elseif ($tipus === 'szakember') {
            $tabla = 'szakemberek';
        } else {
            $errors[] = "Érvénytelen felhasználó típus.";
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT id FROM $tabla WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $token = bin2hex(random_bytes(16));
                $lejar = date('Y-m-d H:i:s', time() + 3600); // 1 óra

                $stmt = $pdo->prepare("INSERT INTO jelszo_visszaallitas (email, token, lejarati_ido) VALUES (:email, :token, :lejar)");
                $stmt->execute([
                    ':email' => $email,
                    ':token' => $token,
                    ':lejar' => $lejar
                ]);

                $url = "https://masszazsportal.hu/uj_jelszo.php?token=$token";

                $mail = new PHPMailer\PHPMailer\PHPMailer();
                $mail->setFrom('info@masszazsportal.hu', 'Masszázsportál');
                $mail->addAddress($email);
                $mail->Subject = "Jelszó visszaállítás";
                $mail->isHTML(true);
                $mail->Body = "Kérlek, állítsd be új jelszavad az alábbi linkre kattintva:<br><br>
                               <a href='$url'>$url</a><br><br>
                               A link 1 órán belül lejár.";

                if ($mail->send()) {
                    $success = true;
                } else {
                    $errors[] = "Nem sikerült elküldeni az e-mailt. Próbáld újra később.";
                }
            } else {
                $errors[] = "Nem található felhasználó ezzel az e-mail címmel.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Jelszó emlékeztető</title>
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
    <h2>Jelszó visszaállítás kérése</h2>

    <?php if ($success): ?>
        <p class="success">E-mail elküldve! Kérlek, nézd meg a beérkező üzeneteidet.</p>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <label>E-mail cím:</label>
        <input type="email" name="email" required>

        <label>Felhasználó típusa:</label>
        <select name="felhasznalo_tipus" required>
            <option value="">-- Válassz --</option>
            <option value="vendeg">Vendég</option>
            <option value="szakember">Szakember</option>
        </select>

        <button type="submit">Küldés</button>
    </form>
</body>
</html>