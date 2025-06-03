<?php
$host = 'localhost';
$dbname = 'c83000masszazsportal_db';
$username = 'c83000mp_user';
$password = 'Gol38Gota@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Adatbázis hiba: " . $e->getMessage());
    die("Az oldal jelenleg nem elérhető. Próbálkozzon később.");
}
?>