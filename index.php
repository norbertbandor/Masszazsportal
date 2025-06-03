<?php
require_once 'config.php';
session_start();

// Véletlenszerű 3 jóváhagyott szakember
$stmt = $pdo->query("SELECT * FROM szakemberek WHERE visszaigazolt = 1 ORDER BY RAND() LIMIT 3");
$szakemberek = $stmt->fetchAll();

// Legfrissebb 3 blogbejegyzés
$blogStmt = $pdo->query("SELECT * FROM blog ORDER BY letrehozas_datuma DESC LIMIT 3");
$blogok = $blogStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Masszázsportál – Megbízható masszőrök, csontkovácsok és reflexológusok</title>
    <meta name="description" content="Találj megbízható, jóváhagyott szakembereket a Masszázsportálon. Böngéssz hitelesített masszőrök, csontkovácsok és reflexológusok között, vagy regisztrálj vendégként.">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; margin: 0; padding: 0; background-color: #f8f8f8; }
        header, footer { background-color: #002d5a; color: white; text-align: center; padding: 20px; }
        nav { background-color: #004080; padding: 10px; text-align: center; }
        nav a { color: white; margin: 0 10px; text-decoration: none; font-weight: bold; }
        .tartalom { max-width: 1000px; margin: auto; padding: 20px; }
        .szakasz { margin-bottom: 40px; }
        .szakember, .blog-kartya {
            background: white; border-radius: 10px; padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05); margin-bottom: 20px;
        }
        .szakember h3, .blog-kartya h3 { margin-top: 0; color: #002d5a; }
        .gomb {
            display: inline-block; padding: 10px 20px; background-color: #002d5a;
            color: white; border-radius: 6px; text-decoration: none; font-weight: bold;
        }
        .gomb:hover { background-color: #004080; }
        .blog-datum { font-size: 0.9em; color: #666; margin-bottom: 10px; }
        .also-linkek { text-align: center; margin-top: 30px; }
        .also-linkek a { color: #002d5a; text-decoration: none; margin: 0 10px; }
        .also-linkek a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<header>
    <h1>Masszázsportál</h1>
    <?php if (isset($_SESSION['vendeg_belepve']) || isset($_SESSION['szakember_belepve'])): ?>
        <nav>
            <a href="profil.php">Profilom</a>
            <a href="profil_szerkesztes_<?= isset($_SESSION['vendeg_belepve']) ? 'vendeg' : 'szakember' ?>.php">Adatok módosítása</a>
            <a href="profil_torles.php">Profil törlése</a>
            <a href="kijelentkezes.php">Kijelentkezés</a>
        </nav>
    <?php else: ?>
        <nav>
            <a href="belepes.php">Bejelentkezés</a>
            <a href="vendeg_regisztracio.php">Vendég regisztráció</a>
            <a href="masszor_regisztracio.php">Szakember regisztráció</a>
        </nav>
    <?php endif; ?>
</header>

<div class="tartalom">
    <div class="szakasz">
        <h2>Üdvözlünk a Masszázsportálon! 💆‍♂️💆‍♀️</h2>
        <p>A Masszázsportál célja, hogy megbízható, jóváhagyott szakembereket kössön össze a masszázs iránt érdeklődő vendégekkel. Legyen szó gyógy-, sport-, relaxációs vagy egyéb típusú masszázsról, nálunk megtalálhatod a hozzád legközelebbi, hitelesített szolgáltatót.</p>
        <p>Vendégként lehetőséged van regisztrálni, értékelést írni, és szűrni a számodra legfontosabb szempontok alapján. Szakemberként pedig egy ingyenes, jóváhagyást követően megjelenő profil segítségével juthatsz el új vendégekhez.</p>
        <p><a class="gomb" href="kereses.php">🔍 Szakember keresése</a></p>
    </div>

    <div class="szakasz">
        <h2>✨ Véletlenszerű szakemberek</h2>
        <?php if ($szakemberek): ?>
            <?php foreach ($szakemberek as $s): ?>
                <div class="szakember">
                    <h3><?= htmlspecialchars($s['nev']) ?> (<?= htmlspecialchars($s['tipus']) ?>)</h3>
                    <p><?= nl2br(htmlspecialchars($s['bemutatkozas'])) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Jelenleg nincs jóváhagyott szakember az adatbázisban.</p>
        <?php endif; ?>
    </div>

    <div class="szakasz">
        <h2>📰 Legfrissebb blogbejegyzések</h2>
        <?php if ($blogok): ?>
            <?php foreach ($blogok as $b): ?>
                <article class="blog-kartya">
                    <h3><?= htmlspecialchars($b['cim']) ?></h3>
                    <div class="blog-datum">🗓️ <?= date('Y. m. d.', strtotime($b['letrehozas_datuma'])) ?></div>
                    <p><?= nl2br(htmlspecialchars(mb_substr($b['tartalom'], 0, 200))) ?>...</p>
                </article>
            <?php endforeach; ?>
            <p style="text-align:center;"><a class="gomb" href="blog.php">📚 További bejegyzések</a></p>
        <?php else: ?>
            <p>Még nem született blogbejegyzés.</p>
        <?php endif; ?>
    </div>

    <div class="also-linkek">
        <a href="aszf.php">Általános Szerződési Feltételek</a> |
        <a href="impresszum.php">Impresszum</a>
    </div>
</div>

</body>
</html>