<?php

require __DIR__ . DIRECTORY_SEPARATOR . "lib.php";
admin_boot();
admin_require_login();

$pub = data_read("pubblicazioni", []);
$gare = data_read("gare", []);
$gaf = data_read("gaf", []);
$gam = data_read("gam", []);

ob_start();
?>
<p class="admin-kicker">Ginnastica Meda</p>
<h1 class="admin-title">Cosa vuoi modificare?</h1>
<p class="admin-lead">I dati sono salvati in JSON e aggiornano subito le pagine pubbliche.</p>
<div class="admin-grid">
  <a class="admin-card" href="pubblicazioni.php">
    <div>
      <p class="admin-kicker">Trasparenza</p>
      <h2>Pubblicazioni</h2>
      <p>Contributi ex L.124/2017</p>
    </div>
    <span class="admin-count"><?= count($pub) ?> anni</span>
  </a>
  <a class="admin-card" href="gare.php">
    <div>
      <p class="admin-kicker">Agonistica</p>
      <h2>Gare</h2>
      <p>Calendario e risultati</p>
    </div>
    <span class="admin-count"><?= count($gare) ?> gare</span>
  </a>
  <a class="admin-card" href="gaf.php">
    <div>
      <p class="admin-kicker">Atlete</p>
      <h2>GAF</h2>
      <p>Ginnastica artistica femminile</p>
    </div>
    <span class="admin-count"><?= count($gaf) ?> atlete</span>
  </a>
  <a class="admin-card" href="gam.php">
    <div>
      <p class="admin-kicker">Atleti</p>
      <h2>GAM</h2>
      <p>Ginnastica artistica maschile</p>
    </div>
    <span class="admin-count"><?= count($gam) ?> atleti</span>
  </a>
</div>
<?php
admin_layout("Dashboard", ob_get_clean(), ["nav" => true, "wide" => true]);
