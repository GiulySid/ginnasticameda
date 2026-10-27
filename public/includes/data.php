<?php

function data_h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

function data_root() {
    return dirname(__DIR__) . DIRECTORY_SEPARATOR . "data";
}

function data_path($name) {
    $safe = preg_replace("/[^a-z0-9_-]/i", "", (string) $name);
    return data_root() . DIRECTORY_SEPARATOR . $safe . ".json";
}

function data_read($name, $default = []) {
    $path = data_path($name);
    if (!is_file($path)) {
        return $default;
    }
    $raw = file_get_contents($path);
    if ($raw === false || $raw === "") {
        return $default;
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $default;
}

function data_write($name, $payload) {
    $dir = data_root();
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return false;
    }
    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }
    $path = data_path($name);
    $tmp = $path . "." . bin2hex(random_bytes(4)) . ".tmp";
    if (file_put_contents($tmp, $json . "\n", LOCK_EX) === false) {
        return false;
    }
    if (!rename($tmp, $path)) {
        @unlink($tmp);
        return false;
    }
    return data_export_js();
}

function data_export_js() {
    $static = data_read("static", ["corsi" => [], "giorni" => [], "team" => []]);
    $gaf = data_read("gaf", []);
    $gam = data_read("gam", []);
    $gare = data_read("gare", []);
    $pub = data_read("pubblicazioni", []);

    $payload = [
        "corsi" => $static["corsi"] ?? [],
        "giorni" => $static["giorni"] ?? ["Lun", "Mar", "Mer", "Gio", "Ven", "Sab"],
        "gaf" => array_map(function ($row) {
            return [$row["nome"] ?? "", $row["nascita"] ?? ""];
        }, $gaf),
        "gam" => array_map(function ($row) {
            return [$row["nome"] ?? "", $row["nascita"] ?? ""];
        }, $gam),
        "gare" => array_map(function ($row) {
            return [
                $row["nome"] ?? "",
                $row["data"] ?? "",
                $row["luogo"] ?? "",
                $row["risultati"] ?? "",
            ];
        }, $gare),
        "team" => $static["team"] ?? [],
        "pubblicazioni" => array_map(function ($year) {
            $voci = [];
            foreach ($year["voci"] ?? [] as $voce) {
                $voci[] = [$voce["voce"] ?? "", $voce["importo"] ?? ""];
            }
            return [
                "anno" => (int) ($year["anno"] ?? 0),
                "voci" => $voci,
            ];
        }, $pub),
    ];

    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }

    $js = "window.GM_DATA = {$json};\n\n";
    $js .= "window.GM_formatDate = (iso) => {\n";
    $js .= "  if (!iso) return \"\";\n";
    $js .= "  const [y, m, d] = iso.split(\"-\").map(Number);\n";
    $js .= "  const months = [\n";
    $js .= "    \"gennaio\", \"febbraio\", \"marzo\", \"aprile\", \"maggio\", \"giugno\",\n";
    $js .= "    \"luglio\", \"agosto\", \"settembre\", \"ottobre\", \"novembre\", \"dicembre\",\n";
    $js .= "  ];\n";
    $js .= "  return `\${d} \${months[m - 1]} \${y}`;\n";
    $js .= "};\n";

    $out = dirname(__DIR__) . DIRECTORY_SEPARATOR . "js" . DIRECTORY_SEPARATOR . "data.js";
    $tmp = $out . "." . bin2hex(random_bytes(4)) . ".tmp";
    if (file_put_contents($tmp, $js, LOCK_EX) === false) {
        return false;
    }
    if (!rename($tmp, $out)) {
        @unlink($tmp);
        return false;
    }
    return true;
}
