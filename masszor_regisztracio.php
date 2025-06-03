<?php include 'fejlec.php'; ?>

<h2>Szakember regisztráció</h2>
<form action="masszor_regisztracio_mentes.php" method="post" enctype="multipart/form-data">
    Teljes név:<br>
    <input type="text" name="teljes_nev" required><br><br>

    Email cím:<br>
    <input type="email" name="email" required><br><br>

    Jelszó:<br>
    <input type="password" name="jelszo" required><br><br>

    Szakma típusa (többet is választhat):<br>
    <br>
    <input type="checkbox" name="szakma_tipus[]" value="Masszőr" onchange="frissitMasszazsReszt()"> Masszőr<br>
    <input type="checkbox" name="szakma_tipus[]" value="Gyógytornász"> Gyógytornász<br>
    <input type="checkbox" name="szakma_tipus[]" value="Reflexológus"> Reflexológus<br>
    <input type="checkbox" name="szakma_tipus[]" value="Egyéb"> Egyéb<br><br>

    <div id="masszazsResz" style="display:none;">
        Masszázs típus(ok):<br>
        <input type="checkbox" name="masszazs_tipusok[]" value="Gyógy"> Gyógy<br>
        <input type="checkbox" name="masszazs_tipusok[]" value="Sport"> Sport<br>
        <input type="checkbox" name="masszazs_tipusok[]" value="Svéd"> Svéd<br>
        <input type="checkbox" name="masszazs_tipusok[]" value="Nyirok"> Nyirok<br>
        <input type="checkbox" name="masszazs_tipusok[]" value="Thai"> Thai<br>
        <input type="checkbox" name="masszazs_tipusok[]" value="Egyéb" id="egyebMasszazs" onchange="toggleEgyebMezo()"> Egyéb<br>
        <div id="egyebMezo" style="display:none;">
            Egyéb megnevezése:<br>
            <input type="text" name="masszazs_egyeb"><br>
        </div><br>
    </div>

    Nem:<br>
    <select name="nem" required>
        <option value="Férfi">Férfi</option>
        <option value="Nő">Nő</option>
    </select><br><br>

    <input type="checkbox" id="telephelyCheck" name="telephelyen_fogad" onchange="toggleTelephely()"> Telephelyen fogad<br>
    <div id="telephelyMezo" style="display:none;">
        Telephely címe:<br>
        <input type="text" name="telephely" placeholder="Irányítószám, város, utca, házszám"><br><br>
    </div>

    <input type="checkbox" id="hazhozCheck" name="hazhoz_is_megy" onchange="toggleHazhoz()"> Házhoz is megyek<br>
    <div id="hazhozMezok" style="display:none;">
        Városok (pl. Budapest, Vác):<br>
        <input type="text" name="hazhoz_varosok" placeholder="Városok vesszővel elválasztva"><br><br>
        Kiszállás díja (Ft, ha nincs írja: 0):<br>
        <input type="text" name="kiszallasi_dij" placeholder="Pl. 2000 vagy 0"><br><br>
    </div>

    30 perc ára (nem kötelező):<br>
    <input type="text" name="ar_30perc" placeholder="Pl. 5000 Ft"><br><br>

    45 perc ára (nem kötelező):<br>
    <input type="text" name="ar_45perc" placeholder="Pl. 6500 Ft"><br><br>

    60 perc ára (nem kötelező):<br>
    <input type="text" name="ar_60perc" placeholder="Pl. 8000 Ft"><br><br>

    90 perc ára (nem kötelező):<br>
    <input type="text" name="ar_90perc" placeholder="Pl. 10000 Ft"><br><br>

    Profilkép feltöltése:<br>
    <input type="file" name="profilkep" id="profilkep" onchange="document.getElementById('profilkep_nev').textContent = this.files[0] ? this.files[0].name : '';">
    <span id="profilkep_nev"></span><br><br>

    Végzettséget igazoló dokumentum feltöltése:<br>
    <input type="file" name="dokumentum" id="dokumentum" onchange="document.getElementById('dokumentum_nev').textContent = this.files[0] ? this.files[0].name : '';">
    <span id="dokumentum_nev"></span><br><br>

    Rövid bemutatkozás (max. 500 karakter):<br>
    <textarea name="bemutatkozas" maxlength="500" rows="4" cols="50"></textarea><br><br>

    <input type="checkbox" name="aszf_elfogadasa" required>
    Elolvastam és elfogadom az <a href="aszf.php" target="_blank">Általános Szerződési Feltételeket</a> és az <a href="adatkezelesi_tajekoztato.php" target="_blank">Adatkezelési tájékoztatót</a>.<br><br>

    Tudomásul veszem, hogy a végzettséget igazoló dokumentum a jóváhagyást követően törlésre kerül a rendszerből, azt nem tároljuk.<br><br>

    <input type="submit" value="Regisztráció">
</form>

<script>
function frissitMasszazsReszt() {
    var checkboxes = document.querySelectorAll('input[name="szakma_tipus[]"]');
    var masszazsDiv = document.getElementById("masszazsResz");
    var masszorKivalasztva = false;

    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked && checkbox.value === "Masszőr") {
            masszorKivalasztva = true;
        }
    });

    masszazsDiv.style.display = masszorKivalasztva ? "block" : "none";
}

function toggleEgyebMezo() {
    document.getElementById('egyebMezo').style.display = 
        document.getElementById('egyebMasszazs').checked ? 'block' : 'none';
}

function toggleTelephely() {
    document.getElementById('telephelyMezo').style.display = 
        document.getElementById('telephelyCheck').checked ? 'block' : 'none';
}

function toggleHazhoz() {
    document.getElementById('hazhozMezok').style.display = 
        document.getElementById('hazhozCheck').checked ? 'block' : 'none';
}
</script>

<?php include 'lablec.php'; ?>
