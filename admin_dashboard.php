<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['admin_belepve']) || $_SESSION['admin_belepve'] !== true) {
    header('Location: admin_belepes.php');
    exit;
}

// Példa a jóváhagyásra váró szakemberek számának lekérésére
$stmt = $pdo->query("SELECT COUNT(*) FROM szakemberek WHERE visszaigazolt = 0");
$jovahagyasra_var = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin vezérlőpult</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #002d5a;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .tartalom {
            padding: 30px;
            max-width: 600px;
            margin: auto;
            background-color: white;
            margin-top: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #002d5a;
            text-align: center;
        }
        .gomb {
            display: block;
            width: 100%;
            padding: 12px;
            margin: 15px 0;
            background-color: #002d5a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
        }
        .gomb:hover {
            background-color: #004080;
        }
        .info {
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>
<body>
<header>
    <h1>🔧 Admin felület – Masszázsportál</h1>
</header>

<div class="tartalom">
    <h2>Üdvözlünk, Admin!</h2>

    <a class="gomb" href="admin_jelszo_valtoztatas.php">🔑 Jelszó módosítása</a>
    <a class="gomb" href="blog_uj.php">📝 Új blogbejegyzés létrehozása</a>
    <a class="gomb" href="blog_kezeles.php">📚 Blogbejegyzések kezelése</a>

    <?php if ($jovahagyasra_var > 0): ?>
        <div class="info">
            ✅ Jelenleg <strong><?= $jovahagyasra_var ?></strong> szakember vár jóváhagyásra.
        </div>
    <?php else: ?>
        <div class="info">
            Jelenleg nincs jóváhagyásra váró szakember.
        </div>
    <?php endif; ?>
</div>
</body>
</html>