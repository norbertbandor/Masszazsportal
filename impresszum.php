<?php include 'fejlec.php'; ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Impresszum</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial; max-width: 800px; margin: auto; padding: 20px; line-height: 1.6; }
        h2 { color: #002d5a; }
        .cimkek { font-weight: bold; margin-top: 15px; }
        .email, .telefon { user-select: none; unicode-bidi: bidi-override; direction: rtl; }
    </style>
</head>
<body>
    <h2>Impresszum</h2>

    <p><span class="cimkek">Név:</span><br>Bandor Norbert</p>

    <p><span class="cimkek">E-mail:</span><br>
        <span class="email">uh.latropzsazzam@ofni</span>
        <noscript>[JavaScript szükséges az e-mail megjelenítéséhez]</noscript>
    </p>

    <p><span class="cimkek">Telefonszám:</span><br>
        <span class="telefon">2654 794 03 63+</span>
        <noscript>[JavaScript szükséges a telefonszám megjelenítéséhez]</noscript>
    </p>

    <script>
        // E-mail megjelenítése emberi formában
        document.querySelector('.email').innerText = 'info@masszazsportal.hu';
        // Telefonszám visszafordítása
        document.querySelector('.telefon').innerText = '+36 30 497 4652';
    </script>

</body>
</html>