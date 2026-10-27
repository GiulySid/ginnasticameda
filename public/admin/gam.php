<?php

require __DIR__ . DIRECTORY_SEPARATOR . "lib.php";
admin_boot();
admin_require_login();

$rows = data_read("gam", []);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    admin_csrf_check();
    $nomi = $_POST["nome"] ?? [];
    $nascite = $_POST["nascita"] ?? [];
    if (!is_array($nomi)) {
        $nomi = [];
    }

    $payload = [];
    foreach ($nomi as $i => $nome) {
        $nome = trim((string) $nome);
        $nascita = trim((string) ($nascite[$i] ?? ""));
        if ($nome === "" && $nascita === "") {
            continue;
        }
        $payload[] = ["nome" => $nome, "nascita" => $nascita];
    }

    usort($payload, function ($a, $b) {
        return strcasecmp($a["nome"] ?? "", $b["nome"] ?? "");
    });

    if (!data_write("gam", $payload)) {
        admin_flash("error", "Salvataggio non riuscito. Controlla i permessi di scrittura su /data e /js.");
    } else {
        admin_flash("success", "Elenco GAM aggiornato.");
    }
    header("Location: gam.php");
    exit;
}

if (!$rows) {
    $rows = [["nome" => "", "nascita" => ""]];
}

ob_start();
?>
<a class="admin-back" href="index.php">← Contenuti</a>
<p class="admin-kicker">Atleti</p>
<h1 class="admin-title">GAM</h1>
<p class="admin-lead">Nome completo e data di nascita (AAAA-MM-GG).</p>

<form class="admin-form" method="post">
  <?= admin_csrf_field() ?>
  <div class="admin-rows" id="rows">
    <?php foreach ($rows as $row) { ?>
      <div class="admin-row admin-row--athlete" data-row>
        <label class="admin-field admin-field--wide">
          <span>Nome</span>
          <input type="text" name="nome[]" value="<?= admin_h($row["nome"] ?? "") ?>" required>
        </label>
        <label class="admin-field">
          <span>Nascita</span>
          <input type="date" name="nascita[]" value="<?= admin_h($row["nascita"] ?? "") ?>">
        </label>
        <button class="admin-btn-ghost" type="button" data-remove-row>Rimuovi</button>
      </div>
    <?php } ?>
  </div>

  <template id="row-template">
    <div class="admin-row admin-row--athlete" data-row>
      <label class="admin-field admin-field--wide">
        <span>Nome</span>
        <input type="text" name="nome[]" required>
      </label>
      <label class="admin-field">
        <span>Nascita</span>
        <input type="date" name="nascita[]">
      </label>
      <button class="admin-btn-ghost" type="button" data-remove-row>Rimuovi</button>
    </div>
  </template>

  <div class="admin-actions">
    <button class="admin-btn-ghost" type="button" data-add-row="#rows" data-template="#row-template">Aggiungi atleta</button>
    <button class="admin-btn" type="submit">Salva</button>
  </div>
</form>
<?php
admin_layout("GAM", ob_get_clean(), ["nav" => true, "wide" => true]);
