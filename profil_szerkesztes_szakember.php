<?php
session_start();
require_once 'config.php';
require_once 'fejlec.php';

if (!isset($_SESSION['felhasznalo_id']) || $_SESSION['felhasznalo_tipus'] !== 'szakember') {
    header('Location: index.php');
    exit;
}

$id = $_SESSION['felhasznalo_id'];
$stmt = $pdo->prepare("SELECT * FROM szakemberek WHERE id = ?");
$stmt->execute([$id]);
$szakember = $stmt->fetch();

$uzenet = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telepules = trim($_POST['telepules']);
    $tipus = $_POST['tipus'];
    $masszazs_tipusok = $_POST['masszazs_tipus'] ?? [];
    $egyeb_tipus = trim($_POST['egyeb_masszazs_tipus'] ?? '');
    if (in_array('egyeb', $masszazs_tipusok) && !empty($egyeb_tipus)) {
        $masszazs_tipusok[] = 'egyéb: ' . $egyeb_tipus;
    }
    $masszazs_tipus = implode(', ', array_filter($masszazs_tipusok, fn($t) => $t !== 'egyeb'));

    $nem = $_POST['nem'];
    $hazhoz = isset($_POST['hazhoz']) ? 1 : 0;
    $fogad_telephelyen = isset($_POST['fogad_telephelyen']) ? 1 : 0;
    $telephely_cim = trim($_POST['telephely_cim'] ?? '');
    $hazhoz_varosok = trim($_POST['hazhoz_varosok'] ?? '');
    $ar_30perc = trim($_POST['ar_30perc'] ?? '');
    $ar_45perc = trim($_POST['ar_45perc'] ?? '');
    $ar_60perc = trim($_POST['ar_60perc'] ?? '');
    $ar_90perc = trim($_POST['ar_90perc'] ?? '');
    $kiszallasi_dij = trim($_POST['kiszallasi_dij'] ?? '');
    $bemutatkozas = trim($_POST['bemutatkozas']);
    $telefonszam = trim($_POST['telefonszam']);

    $stmt = $pdo->prepare("UPDATE szakemberek SET telepules = ?, tipus = ?, masszazs_tipus = ?, nem = ?, hazhoz = ?, fogad_telephelyen = ?, telephely_cim = ?, hazhoz_varosok = ?, ar_30perc = ?, ar_45perc = ?, ar_60perc = ?, ar_90perc = ?, kiszallasi_dij = ?, bemutatkozas = ?, telefonszam = ? WHERE id = ?");
    $stmt->execute([$telepules, $tipus, $masszazs_tipus, $nem, $hazhoz, $fogad_telephelyen, $telephely_cim, $hazhoz_varosok, $ar_30perc, $ar_45perc, $ar_60perc, $ar_90perc, $kiszallasi_dij, $bemutatkozas, $telefonszam, $id]);

    $uzenet = 'A profilod frissítve lett.';
    $stmt->execute([$id]);
    $stmt->execute([$id]);
    header("Refresh:0");
    exit;
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Profil szerkesztése</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; max-width: 900px; margin: auto; padding: 20px; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 20px; background: #002d5a; color: white; border: none; border-radius: 5px; }
        .kiscim { margin-top: 15px; font-weight: bold; }
    </style>
    <script>
        function toggleMasszazs() {
            const tipus = document.getElementById('tipus').value;
            document.getElementById('masszazs_tipusok').style.display = (tipus === 'masszőr') ? 'block' : 'none';
        }

        function toggleEgyeb() {
            document.getElementById('egyeb_mezo').style.display = document.getElementById('egyeb').checked ? 'block' : 'none';
        }

        function toggleTelephely() {
            document.getElementById('telephely_adatok').style.display = document.getElementById('fogad_telephelyen').checked ? 'block' : 'none';
        }

        function toggleHazhoz() {
            document.getElementById('hazhoz_adatok').style.display = document.getElementById('hazhoz').checked ? 'block' : 'none';
        }

        window.onload = function () {
            toggleMasszazs();
            toggleEgyeb();
            toggleTelephely();
            toggleHazhoz();
        };
    </script>
</head>
<body>
    <h2>👤 Profil szerkesztése</h2>

    <?php if (!empty($uzenet)): ?>
        <p style="color: green;"><?= htmlspecialchars($uzenet) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Település:</label>
        <input type="text" name="telepules" value="<?= htmlspecialchars($szakember['telepules']) ?>" required>

        <label>Szakma típusa:</label>
        <select name="tipus" id="tipus" onchange="toggleMasszazs()" required>
            <?php foreach (['masszőr', 'csontkovács', 'reflexológus', 'egyéb'] as $opcio): ?>
                <option value="<?= $opcio ?>" <?= $szakember['tipus'] === $opcio ? 'selected' : '' ?>><?= ucfirst($opcio) ?></option>
            <?php endforeach; ?>
        </select>

        <div id="masszazs_tipusok" style="display:none;">
            <p class="kiscim">Masszázs típus(ok):</p>
            <?php
                $kijeloltek = explode(', ', $szakember['masszazs_tipus']);
                $valaszthato = ['gyógy', 'sport', 'svéd', 'nyirok', 'thai'];
                foreach ($valaszthato as $tipus):
            ?>
                <label><input type="checkbox" name="masszazs_tipus[]" value="<?= $tipus ?>" <?= in_array($tipus, $kijeloltek) ? 'checked' : '' ?>> <?= ucfirst($tipus) ?></label>
            <?php endforeach; ?>
            <label><input type="checkbox" id="egyeb" name="masszazs_tipus[]" value="egyeb" onchange="toggleEgyeb()"> Egyéb</label>
            <div id="egyeb_mezo" style="display: none;">
                <input type="text" name="egyeb_masszazs_tipus" placeholder="Pl. lávaköves">
            </div>
        </div>

        <label>Nem:</label>
        <select name="nem">
            <option value="férfi" <?= $szakember['nem'] === 'férfi' ? 'selected' : '' ?>>Férfi</option>
            <option value="nő" <?= $szakember['nem'] === 'nő' ? 'selected' : '' ?>>Nő</option>
        </select>

        <label>Telefonszám:</label>
        <input type="text" name="telefonszam" value="<?= htmlspecialchars($szakember['telefonszam']) ?>">

        <label><input type="checkbox" id="fogad_telephelyen" name="fogad_telephelyen" <?= $szakember['fogad_telephelyen'] ? 'checked' : '' ?> onchange="toggleTelephely()"> Telephelyen fogad</label>
        <div id="telephely_adatok" style="display: none;">
            <label>Telephely címe:</label>
            <input type="text" name="telephely_cim" value="<?= htmlspecialchars($szakember['telephely_cim']) ?>">
        </div>

        <label><input type="checkbox" id="hazhoz" name="hazhoz" <?= $szakember['hazhoz'] ? 'checked' : '' ?> onchange="toggleHazhoz()"> Házhoz is megy</label>
        <div id="hazhoz_adatok" style="display: none;">
            <label>Városok, ahová kiszáll:</label>
            <input type="text" name="hazhoz_varosok" value="<?= htmlspecialchars($szakember['hazhoz_varosok']) ?>">
        </div>

        <label class="kiscim">Árak (nem kötelező):</label>
        <label>30 perc:</label><input type="text" name="ar_30perc" value="<?= htmlspecialchars($szakember['ar_30perc']) ?>">
        <label>45 perc:</label><input type="text" name="ar_45perc" value="<?= htmlspecialchars($szakember['ar_45perc']) ?>">
        <label>60 perc:</label><input type="text" name="ar_60perc" value="<?= htmlspecialchars($szakember['ar_60perc']) ?>">
        <label>90 perc:</label><input type="text" name="ar_90perc" value="<?= htmlspecialchars($szakember['ar_90perc']) ?>">
        <label>Kiszállási díj:</label><input type="text" name="kiszallasi_dij" value="<?= htmlspecialchars($szakember['kiszallasi_dij']) ?>">

        <label>Bemutatkozás:</label>
        <textarea name="bemutatkozas" maxlength="500" rows="4"><?= htmlspecialchars($szakember['bemutatkozas']) ?></textarea>

        <button type="submit">Mentés</button>
    </form>
</body>
</html>