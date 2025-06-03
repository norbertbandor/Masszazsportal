<?php
require_once 'config.php';
require_once 'fejlec.php';

$szures = [];
$parameterek = [];

if (!empty($_GET['tipus'])) {
    $szures[] = "tipus = ?";
    $parameterek[] = $_GET['tipus'];
}

if (!empty($_GET['masszazs_tipus'])) {
    $szures[] = "masszazs_tipus LIKE ?";
    $parameterek[] = '%' . $_GET['masszazs_tipus'] . '%';
}

if (!empty($_GET['nem'])) {
    $szures[] = "nem = ?";
    $parameterek[] = $_GET['nem'];
}

if (!empty($_GET['hazhoz'])) {
    $szures[] = "hazhoz = 1";
}

if (!empty($_GET['varos'])) {
    $szures[] = "(telepules LIKE ? OR hazhoz_varosok LIKE ?)";
    $parameterek[] = '%' . $_GET['varos'] . '%';
    $parameterek[] = '%' . $_GET['varos'] . '%';
}

$szures[] = "visszaigazolt = 1";

$where = implode(" AND ", $szures);
$query = "SELECT * FROM szakemberek";
if (!empty($where)) {
    $query .= " WHERE $where";
}
$stmt = $pdo->prepare($query);
$stmt->execute($parameterek);
$talalatok = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Szakember keresése</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; max-width: 900px; margin: auto; padding: 20px; }
        h2 { color: #002d5a; }
        label { display: block; margin-top: 10px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; }
        .talalat { border: 1px solid #ccc; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .talalat h3 { margin: 0 0 5px; }
        .ikon { margin-right: 6px; }
        button { margin-top: 10px; padding: 8px 12px; background: #002d5a; color: white; border: none; border-radius: 5px; }
        .linkek { margin-top: 20px; }
        .linkek a { margin-right: 10px; }
    </style>
</head>
<body>
    <h2>🔍 Szakember keresése</h2>

    <form method="get">
        <label>👨‍⚕️ Szakma típusa:
            <select name="tipus" onchange="this.form.submit()">
                <option value="">-- Mindegy --</option>
                <option value="masszőr" <?= ($_GET['tipus'] ?? '') === 'masszőr' ? 'selected' : '' ?>>Masszőr</option>
                <option value="csontkovács" <?= ($_GET['tipus'] ?? '') === 'csontkovács' ? 'selected' : '' ?>>Csontkovács</option>
                <option value="reflexológus" <?= ($_GET['tipus'] ?? '') === 'reflexológus' ? 'selected' : '' ?>>Reflexológus</option>
                <option value="egyéb" <?= ($_GET['tipus'] ?? '') === 'egyéb' ? 'selected' : '' ?>>Egyéb</option>
            </select>
        </label>

        <?php if (($_GET['tipus'] ?? '') === 'masszőr'): ?>
            <label>💆‍♀️ Masszázs típus(ok):
                <select name="masszazs_tipus">
                    <option value="">-- Mindegy --</option>
                    <?php foreach (['gyógy', 'sport', 'svéd', 'nyirok', 'thai'] as $t): ?>
                        <option value="<?= $t ?>" <?= ($_GET['masszazs_tipus'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php endif; ?>

        <label>🚻 Nem:
            <select name="nem">
                <option value="">-- Mindegy --</option>
                <option value="férfi" <?= ($_GET['nem'] ?? '') === 'férfi' ? 'selected' : '' ?>>Férfi</option>
                <option value="nő" <?= ($_GET['nem'] ?? '') === 'nő' ? 'selected' : '' ?>>Nő</option>
            </select>
        </label>

        <label><input type="checkbox" name="hazhoz" value="1" <?= isset($_GET['hazhoz']) ? 'checked' : '' ?>> 🏠 Házhoz megy</label>

        <label>📍 Város:
            <input type="text" name="varos" value="<?= htmlspecialchars($_GET['varos'] ?? '') ?>">
        </label>

        <button type="submit">🔍 Keresés indítása</button>
        <a href="kereses.php"><button type="button">🔄 Szűrés alaphelyzetbe</button></a>
        <a href="index.php"><button type="button">🏠 Vissza a főoldalra</button></a>
    </form>

    <?php if ($talalatok): ?>
        <?php foreach ($talalatok as $talalat): ?>
            <div class="talalat">
                <h3><?= htmlspecialchars($talalat['nev']) ?> (<?= htmlspecialchars($talalat['tipus']) ?>)</h3>
                <div><span class="ikon">📍</span>Település: <?= htmlspecialchars($talalat['telepules']) ?></div>
                <?php if (!empty($talalat['masszazs_tipus'])): ?>
                    <div><span class="ikon">💆‍♂️</span>Masszázs típus(ok): <?= htmlspecialchars($talalat['masszazs_tipus']) ?></div>
                <?php endif; ?>
                <div><span class="ikon">🚻</span>Nem: <?= htmlspecialchars($talalat['nem']) ?></div>
                <div><span class="ikon">🏠</span>Házhoz megy: <?= $talalat['hazhoz'] ? 'Igen' : 'Nem' ?></div>
                <?php if (!empty($talalat['hazhoz_varosok'])): ?>
                    <div><span class="ikon">🚗</span>Kiszállási városok: <?= htmlspecialchars($talalat['hazhoz_varosok']) ?></div>
                <?php endif; ?>
                <?php if (!empty($talalat['telephely_cim'])): ?>
                    <div><span class="ikon">🏢</span>Telephely címe: <?= htmlspecialchars($talalat['telephely_cim']) ?></div>
                <?php endif; ?>
                <?php if (!empty($talalat['bemutatkozas'])): ?>
                    <div><span class="ikon">📄</span><em><?= nl2br(htmlspecialchars($talalat['bemutatkozas'])) ?></em></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Nincs találat a megadott szűrési feltételekkel.</p>
    <?php endif; ?>
</body>
</html>