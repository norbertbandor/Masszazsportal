<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['admin_belepve']) || $_SESSION['admin_belepve'] !== true) {
    header('Location: admin_belepes.php');
    exit;
}

// Bejegyzés törlése, ha törlés érkezett
if (isset($_GET['torles']) && is_numeric($_GET['torles'])) {
    $stmt = $pdo->prepare("DELETE FROM blog WHERE id = ?");
    $stmt->execute([$_GET['torles']]);
    header('Location: blog_kezeles.php');
    exit;
}

// Bejegyzések lekérdezése
$stmt = $pdo->query("SELECT * FROM blog ORDER BY letrehozas_datuma DESC");
$bejegyzesek = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Blogbejegyzések kezelése</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; background-color: #f4f4f4; padding: 20px; }
        .doboz { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #002d5a; text-align: center; }
        .kartya {
            border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 15px 0;
            background-color: #fafafa; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .kartya h3 { margin: 0 0 10px 0; color: #002d5a; }
        .datum, .hashtag { font-size: 0.9em; color: #555; }
        .gombok { margin-top: 10px; }
        .gomb {
            display: inline-block; padding: 8px 14px; margin-right: 10px;
            background-color: #002d5a; color: white; border-radius: 5px; text-decoration: none;
        }
        .gomb:hover { background-color: #004080; }
        .gomb.torol { background-color: #a00000; }
        .gomb.torol:hover { background-color: #cc0000; }
    </style>
</head>
<body>
<div class="doboz">
    <h2>📚 Blogbejegyzések kezelése</h2>

    <?php if (empty($bejegyzesek)): ?>
        <p>Nincs még bejegyzés.</p>
    <?php else: ?>
        <?php foreach ($bejegyzesek as $b): ?>
            <div class="kartya">
                <h3><?= htmlspecialchars($b['cim']) ?></h3>
                <div class="datum">🗓️ <?= date('Y. m. d. H:i', strtotime($b['letrehozas_datuma'])) ?></div>
                <?php if ($b['hashtag']): ?>
                    <div class="hashtag"># <?= htmlspecialchars($b['hashtag']) ?></div>
                <?php endif; ?>
                <div class="gombok">
                    <!-- Itt lehetne később szerkesztés is -->
                    <a href="blog_kezeles.php?torles=<?= $b['id'] ?>" class="gomb torol" onclick="return confirm('Biztosan törölni szeretnéd ezt a bejegyzést?')">🗑️ Törlés</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>