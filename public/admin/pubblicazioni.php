<?php

require __DIR__ . DIRECTORY_SEPARATOR . "lib.php";
admin_boot();
admin_require_login();

$rows = data_read("pubblicazioni", []);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    admin_csrf_check();
    $anni = $_POST["anno"] ?? [];
    $voci = $_POST["voce"] ?? [];
    $importi = $_POST["importo"] ?? [];
    if (!is_array($anni)) {
        $anni = [];
    }

    $grouped = [];
    foreach ($anni as $i => $anno) {
        $anno = (int) $anno;
        $voce = trim((string) ($voci[$i] ?? ""));
        $importo = trim((string) ($importi[$i] ?? ""));
        if ($anno < 1990 || $anno > 2100 || ($voce === "" && $importo === "")) {
            continue;
        }
        if (!isset($grouped[$anno])) {
            $grouped[$anno] = ["anno" => $anno, "voci" => []];
        }
        if ($voce !== "" || $importo !== "") {
            $grouped[$anno]["voci"][] = ["voce" => $voce, "importo" => $importo];
        }
    }

    krsort($grouped);
    $payload = array_values($grouped);

    if (!data_write("pubblicazioni", $payload)) {
        admin_flash("error", "Salvataggio non riuscito. Controlla i permessi di scrittura su /data e /js.");
    } else {
        admin_flash("success", "Pubblicazioni aggiornate.");
    }
    header("Location: pubblicazioni.php");
    exit;
}

// Flatten for the editable grid
$flat = [];
foreach ($rows as $year) {
    $voci = $year["voci"] ?? [];
    if (!$voci) {
        $flat[] = ["anno" => $year["anno"] ?? "", "voce" => "", "importo" => ""];
        continue;
    }
    foreach ($voci as $voce) {
        $flat[] = [
            "anno" => $year["anno"] ?? "",
            "voce" => $voce["voce"] ?? "",
            "importo" => $voce["importo"] ?? "",
        ];
    }
}
if (!$flat) {
    $flat[] = ["anno" => date("Y"), "voce" => "", "importo" => ""];
}

ob_start();
?>
<a class="admin-back" href="index.php">← Contenuti</a>
<p class="admin-kicker">Trasparenza</p>
<h1 class="admin-title">Pubblicazioni</h1>
<p class="admin-lead">Una riga per ogni contributo. Stesso anno = raggruppato sulla pagina pubblica.</p>

<form class="admin-form" method="post">
  <?= admin_csrf_field() ?>
  <div class="admin-rows" id="rows">
    <?php foreach ($flat as $row) { ?>
      <div class="admin-row" data-row>
        <label class="admin-field">
          <span>Anno</span>
          <input type="number" name="anno[]" min="1990" max="2100" value="<?= admin_h($row["anno"]) ?>" required>
        </label>
        <label class="admin-field">
          <span>Voce</span>
          <input type="text" name="voce[]" value="<?= admin_h($row["voce"]) ?>" placeholder="5x1000">
        </label>
        <label class="admin-field">
          <span>Importo</span>
          <input type="text" name="importo[]" value="<?= admin_h($row["importo"]) ?>" placeholder="€ 0,00">
        </label>
        <button class="admin-btn-ghost" type="button" data-remove-row>Rimuovi</button>
      </div>
    <?php } ?>
  </div>

  <template id="row-template">
    <div class="admin-row" data-row>
      <label class="admin-field">
        <span>Anno</span>
        <input type="number" name="anno[]" min="1990" max="2100" value="<?= admin_h(date("Y")) ?>" required>
      </label>
      <label class="admin-field">
        <span>Voce</span>
        <input type="text" name="voce[]" placeholder="5x1000">
      </label>
      <label class="admin-field">
        <span>Importo</span>
        <input type="text" name="importo[]" placeholder="€ 0,00">
      </label>
      <button class="admin-btn-ghost" type="button" data-remove-row>Rimuovi</button>
    </div>
  </template>

  <div class="admin-actions">
    <button class="admin-btn-ghost" type="button" data-add-row="#rows" data-template="#row-template">Aggiungi riga</button>
    <button class="admin-btn" type="submit">Salva</button>
  </div>
</form>
<?php
admin_layout("Pubblicazioni", ob_get_clean(), ["nav" => true, "wide" => true]);
