<?php
define('SMTP_HOST', 'smtp.rackhost.hu');
define('SMTP_PORT', 465);
define('SMTP_USER', 'info@masszazsportal.hu');
define('SMTP_PASS', 'mEgno0-qexjir-niqkyr');

$host = 'localhost';
$dbname = 'c83000masszazsportal_db';
$username = 'c83000mp_user';
$password = 'Gol38Gota@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Adatbázis hiba: " . $e->getMessage());
    die("Az oldal jelenleg nem elérhető.");
}
?>