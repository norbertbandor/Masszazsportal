<?php
require_once 'config.php';
session_start();

// Összes blogbejegyzés lekérése (legfrissebb elöl)
$stmt = $pdo->query("SELECT * FROM blog ORDER BY letrehozas_datuma DESC");
$bejegyzesek = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Blogbejegyzések – Masszázsportál</title>
    <meta name="description" content="Ismerd meg a különböző masszázstípusokat, technikákat és előnyöket a Masszázsportál blogjában.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; margin: 0; padding: 0; background-color: #f8f8f8; }
        header { background-color: #002d5a; color: white; text-align: center; padding: 20px; }
        .tartalom { max-width: 900px; margin: auto; padding: 20px; }
        article {
            background: white; border-radius: 10px; padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05); margin-bottom: 30px;
        }
        article h2 { margin-top: 0; color: #002d5a; }
        .datum { font-size: 0.9em; color: #555; margin-bottom: 10px; }
        .hashtag { color: #004080; font-size: 0.9em; margin-top: 10px; }
        .megosztas {
            display: inline-block; padding: 8px 12px; background-color: #3b5998;
            color: white; border-radius: 5px; text-decoration: none;
        }
        .megosztas:hover { background-color: #2d4373; }
        footer {
            text-align: center; font-size: 14px; margin-top: 50px; padding: 20px;
            background-color: #002d5a; color: white;
        }
    </style>
</head>
<body>
<header>
    <h1>📚 Blogbejegyzések</h1>
</header>

<div class="tartalom">
    <?php if (empty($bejegyzesek)): ?>
        <p>Még nincs bejegyzés.</p>
    <?php else: ?>
        <?php foreach ($bejegyzesek as $b): ?>
            <article id="b<?= $b['id'] ?>">
                <h2><?= htmlspecialchars($b['cim']) ?></h2>
                <div class="datum">🗓️ <?= date('Y. m. d.', strtotime($b['letrehozas_datuma'])) ?></div>
                <div><?= nl2br(htmlspecialchars($b['tartalom'])) ?></div>
                <?php if ($b['hashtag']): ?>
                    <div class="hashtag">#<?= htmlspecialchars($b['hashtag']) ?></div>
                <?php endif; ?>
                <p><a class="megosztas" href="https://www.facebook.com/sharer/sharer.php?u=https://masszazsportal.hu/blog.php#b<?= $b['id'] ?>" target="_blank">📤 Megosztás Facebookon</a></p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<footer>
    <a href="index.php" style="color:white;">🏠 Vissza a főoldalra</a>
</footer>
</body>
</html>