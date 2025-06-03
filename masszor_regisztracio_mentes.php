<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Alap adatok begyűjtése
    $teljes_nev = $_POST['teljes_nev'] ?? '';
    $email = $_POST['email'] ?? '';
    $jelszo = $_POST['jelszo'] ?? '';

    if (empty($teljes_nev) || empty($email) || empty($jelszo)) {
        echo 'Minden mező kitöltése kötelező!';
        exit;
    }

    // Itt folytatható az összes többi mező mentése igény szerint

    // Példa lekérdezés (csak a három alap mezővel)
    $stmt = $conn->prepare("INSERT INTO masszorok (nev, email, telefon) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $teljes_nev, $email, $jelszo); // ideiglenesen jelszó megy telefon helyett

    if ($stmt->execute()) {
        echo 'Regisztráció beküldve. Adminisztrátoraink e-mailben értesítik a jóváhagyásról.';
    } else {
        echo 'Hiba történt a regisztráció során.';
    }

    $stmt->close();
    $conn->close();
} else {
    echo 'Érvénytelen kérés.';
}