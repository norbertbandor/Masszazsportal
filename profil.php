<?php
session_start();
require_once 'config.php';
require_once 'fejlec.php';

if (!isset($_SESSION['felhasznalo_id'])) {
    header("Location: index.php");
    exit;
}

$id = $_SESSION['felhasznalo_id'];
$tipus = $_SESSION['felhasznalo_tipus'];

if ($tipus === 'szakember') {
    $stmt = $pdo->prepare("SELECT * FROM szakemberek WHERE id = ?");
} else {
    $stmt = $pdo->prepare("SELECT * FROM vendeg_felhasznalok WHERE id = ?");
}

$stmt->execute([$id]);
$felhasznalo = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Profilom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; max-width: 900px; margin: auto; padding: 20px; }
        h2 { color: #002d5a; }
        .adat { margin-bottom: 10px; }
        .cimke { font-weight: bold; display: inline-block; min-width: 180px; }
        a.button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #002d5a; color: #fff; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <h2>👤 Profilom</h2>

    <?php if ($tipus === 'szakember'): ?>
        <div class="adat"><span class="cimke">Név:</span> <?= htmlspecialchars($felhasznalo['nev']) ?></div>
        <div class="adat"><span class="cimke">E-mail:</span> <?= htmlspecialchars($felhasznalo['email']) ?></div>
        <div class="adat"><span class="cimke">Telefonszám:</span> <?= htmlspecialchars($felhasznalo['telefonszam']) ?></div>
        <div class="adat"><span class="cimke">Település:</span> <?= htmlspecialchars($felhasznalo['telepules']) ?></div>
        <div class="adat"><span class="cimke">Típus:</span> <?= htmlspecialchars($felhasznalo['tipus']) ?></div>
        <?php if (!empty($felhasznalo['masszazs_tipus'])): ?>
            <div class="adat"><span class="cimke">Masszázs típus(ok):</span> <?= htmlspecialchars($felhasznalo['masszazs_tipus']) ?></div>
        <?php endif; ?>
        <div class="adat"><span class="cimke">Nem:</span> <?= htmlspecialchars($felhasznalo['nem']) ?></div>
        <div class="adat"><span class="cimke">Fogad telephelyen:</span> <?= $felhasznalo['fogad_telephelyen'] ? 'Igen' : 'Nem' ?></div>
        <?php if (!empty($felhasznalo['telephely_cim'])): ?>
            <div class="adat"><span class="cimke">Telephely címe:</span> <?= htmlspecialchars($felhasznalo['telephely_cim']) ?></div>
        <?php endif; ?>
        <div class="adat"><span class="cimke">Házhoz megy:</span> <?= $felhasznalo['hazhoz'] ? 'Igen' : 'Nem' ?></div>
        <?php if (!empty($felhasznalo['hazhoz_varosok'])): ?>
            <div class="adat"><span class="cimke">Kiszállási városok:</span> <?= htmlspecialchars($felhasznalo['hazhoz_varosok']) ?></div>
        <?php endif; ?>
        <?php if (!empty($felhasznalo['ar_30perc']) || !empty($felhasznalo['ar_45perc']) || !empty($felhasznalo['ar_60perc']) || !empty($felhasznalo['ar_90perc'])): ?>
            <div class="adat"><span class="cimke">Árak:</span>
                <?php if (!empty($felhasznalo['ar_30perc'])): ?>30 perc: <?= htmlspecialchars($felhasznalo['ar_30perc']) ?> Ft<br><?php endif; ?>
                <?php if (!empty($felhasznalo['ar_45perc'])): ?>45 perc: <?= htmlspecialchars($felhasznalo['ar_45perc']) ?> Ft<br><?php endif; ?>
                <?php if (!empty($felhasznalo['ar_60perc'])): ?>60 perc: <?= htmlspecialchars($felhasznalo['ar_60perc']) ?> Ft<br><?php endif; ?>
                <?php if (!empty($felhasznalo['ar_90perc'])): ?>90 perc: <?= htmlspecialchars($felhasznalo['ar_90perc']) ?> Ft<br><?php endif; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($felhasznalo['kiszallasi_dij'])): ?>
            <div class="adat"><span class="cimke">Kiszállási díj:</span> <?= htmlspecialchars($felhasznalo['kiszallasi_dij']) ?> Ft</div>
        <?php endif; ?>
        <?php if (!empty($felhasznalo['bemutatkozas'])): ?>
            <div class="adat"><span class="cimke">Bemutatkozás:</span><br><?= nl2br(htmlspecialchars($felhasznalo['bemutatkozas'])) ?></div>
        <?php endif; ?>

        <a href="profil_szerkesztes_szakember.php" class="button">Adatok módosítása</a>

    <?php else: ?>
        <div class="adat"><span class="cimke">Név:</span> <?= htmlspecialchars($felhasznalo['nev']) ?></div>
        <div class="adat"><span class="cimke">E-mail:</span> <?= htmlspecialchars($felhasznalo['email']) ?></div>
        <a href="profil_szerkesztes_vendeg.php" class="button">Adatok módosítása</a>
    <?php endif; ?>

    <a href="profil_torles.php" class="button" style="background: darkred;" onclick="return confirm('Biztosan törölni szeretnéd a profilodat? Ez a művelet nem visszavonható!')">Profil törlése</a>
</body>
</html>