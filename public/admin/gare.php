<?php

require __DIR__ . DIRECTORY_SEPARATOR . "lib.php";
admin_boot();
admin_require_login();

$rows = data_read("gare", []);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    admin_csrf_check();
    $nomi = $_POST["nome"] ?? [];
    $date = $_POST["data"] ?? [];
    $luoghi = $_POST["luogo"] ?? [];
    $risultati = $_POST["risultati"] ?? [];
    if (!is_array($nomi)) {
        $nomi = [];
    }

    $payload = [];
    foreach ($nomi as $i => $nome) {
        $nome = trim((string) $nome);
        $data = trim((string) ($date[$i] ?? ""));
        $luogo = trim((string) ($luoghi[$i] ?? ""));
        $ris = trim((string) ($risultati[$i] ?? ""));
        if ($nome === "" && $data === "" && $luogo === "" && $ris === "") {
            continue;
        }
        $payload[] = [
            "nome" => $nome,
            "data" => $data,
            "luogo" => $luogo,
            "risultati" => $ris,
        ];
    }

    usort($payload, function ($a, $b) {
        return strcmp($b["data"] ?? "", $a["data"] ?? "");
    });

    if (!data_write("gare", $payload)) {
        admin_flash("error", "Salvataggio non riuscito. Controlla i permessi di scrittura su /data e /js.");
    } else {
        admin_flash("success", "Gare aggiornate.");
    }
    header("Location: gare.php");
    exit;
}

if (!$rows) {
    $rows = [["nome" => "", "data" => "", "luogo" => "", "risultati" => ""]];
}

ob_start();
?>
<a class="admin-back" href="index.php">← Contenuti</a>
<p class="admin-kicker">Agonistica</p>
<h1 class="admin-title">Gare</h1>
<p class="admin-lead">Nome gara, data (AAAA-MM-GG), luogo e eventuale esito/ente (es. FGI).</p>

<form class="admin-form" method="post">
  <?= admin_csrf_field() ?>
  <div class="admin-rows" id="rows">
    <?php foreach ($rows as $row) { ?>
      <div class="admin-row admin-row--gare" data-row>
        <label class="admin-field admin-field--wide">
          <span>Gara</span>
          <input type="text" name="nome[]" value="<?= admin_h($row["nome"] ?? "") ?>" required>
        </label>
        <label class="admin-field">
          <span>Data</span>
          <input type="date" name="data[]" value="<?= admin_h($row["data"] ?? "") ?>">
        </label>
        <label class="admin-field">
          <span>Luogo</span>
          <input type="text" name="luogo[]" value="<?= admin_h($row["luogo"] ?? "") ?>">
        </label>
        <label class="admin-field">
          <span>Risultati</span>
          <input type="text" name="risultati[]" value="<?= admin_h($row["risultati"] ?? "") ?>" placeholder="FGI">
        </label>
        <button class="admin-btn-ghost" type="button" data-remove-row>Rimuovi</button>
      </div>
    <?php } ?>
  </div>

  <template id="row-template">
    <div class="admin-row admin-row--gare" data-row>
      <label class="admin-field admin-field--wide">
        <span>Gara</span>
        <input type="text" name="nome[]" required>
      </label>
      <label class="admin-field">
        <span>Data</span>
        <input type="date" name="data[]">
      </label>
      <label class="admin-field">
        <span>Luogo</span>
        <input type="text" name="luogo[]">
      </label>
      <label class="admin-field">
        <span>Risultati</span>
        <input type="text" name="risultati[]" placeholder="FGI">
      </label>
      <button class="admin-btn-ghost" type="button" data-remove-row>Rimuovi</button>
    </div>
  </template>

  <div class="admin-actions">
    <button class="admin-btn-ghost" type="button" data-add-row="#rows" data-template="#row-template">Aggiungi gara</button>
    <button class="admin-btn" type="submit">Salva</button>
  </div>
</form>
<?php
admin_layout("Gare", ob_get_clean(), ["nav" => true, "wide" => true]);
