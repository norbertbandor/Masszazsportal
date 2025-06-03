
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav style="background: #002d5a; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center;">
    <div style="font-size: 20px; font-weight: bold;">Masszázsportál</div>
    <div>
        <?php if (!isset($_SESSION['felhasznalo_id'])): ?>
            <a href="belepes.php" style="color: white; margin-right: 15px;">Bejelentkezés</a>
            <a href="vendeg_regisztracio.php" style="color: white; margin-right: 15px;">Vendég regisztráció</a>
            <a href="masszor_regisztracio.php" style="color: white;">Szakember regisztráció</a>
        <?php else: ?>
            <a href="profil.php" style="color: white; margin-right: 15px;">Profilom</a>
            <a href="profil_szerkesztes_<?php echo $_SESSION['felhasznalo_tipus']; ?>.php" style="color: white; margin-right: 15px;">Adatok módosítása</a>
            <a href="kilepes.php" style="color: white;">Kijelentkezés</a>
        <?php endif; ?>
    </div>
</nav>