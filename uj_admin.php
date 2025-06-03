<?php
require_once 'config.php';

// Új admin felhasználó adatai
$felhasznalonev = 'admin';
$jelszo = 'admin123';

// Jelszó hash-elése
$hashedJelszo = password_hash($jelszo, PASSWORD_DEFAULT);

// Tábla törlése és új admin beszúrása
try {
    $pdo->exec("DELETE FROM adminok");

    $stmt = $pdo->prepare("INSERT INTO adminok (felhasznalonev, jelszo) VALUES (?, ?)");
    $stmt->execute([$felhasznalonev, $hashedJelszo]);

    echo "✅ Az admin felhasználó sikeresen létrehozva.<br>";
    echo "👤 Felhasználónév: <strong>admin</strong><br>";
    echo "🔑 Jelszó: <strong>admin123</strong><br>";
    echo "<br><strong>❗ Fontos: az uj_admin.php fájlt most töröld a szerverről biztonsági okokból!</strong>";
} catch (PDOException $e) {
    echo "Hiba történt: " . $e->getMessage();
}
?>