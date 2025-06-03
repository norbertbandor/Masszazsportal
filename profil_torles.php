<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['felhasznalo_id']) || !isset($_SESSION['felhasznalo_tipus'])) {
    header("Location: belepes.php");
    exit;
}

$felhasznalo_id = $_SESSION['felhasznalo_id'];
$tipus = $_SESSION['felhasznalo_tipus'];

if ($tipus === 'vendeg') {
    $stmt = $pdo->prepare("DELETE FROM vendeg_felhasznalok WHERE id = ?");
} elseif ($tipus === 'szakember') {
    $stmt = $pdo->prepare("DELETE FROM szakemberek WHERE id = ?");
} else {
    // ha valamiért nem vendég vagy szakember
    header("Location: index.php");
    exit;
}

if ($stmt->execute([$felhasznalo_id])) {
    session_destroy();
    header("Location: index.php");
    exit;
} else {
    echo "Hiba történt a törlés során. Kérlek, próbáld újra később.";
}