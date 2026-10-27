<?php

require dirname(__DIR__) . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "data.php";

const ADMIN_CREDENTIALS = __DIR__ . DIRECTORY_SEPARATOR . "credentials.php";

function admin_boot() {
    $https = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";
    $script = str_replace("\\", "/", $_SERVER["SCRIPT_NAME"] ?? "/admin/index.php");
    if (preg_match("#^(.*?/admin)(?:/|$)#", $script, $match)) {
        $path = $match[1];
    } else {
        $path = rtrim(dirname($script), "/");
        if ($path === "") {
            $path = "/";
        }
    }

    session_name("gm_admin");
    session_set_cookie_params([
        "lifetime" => 0,
        "path" => $path,
        "secure" => $https,
        "httponly" => true,
        "samesite" => "Lax",
    ]);
    session_start();

    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: same-origin");
    header("Cache-Control: no-store");
}

function admin_h($value) {
    return data_h($value);
}

function admin_credentials() {
    if (!is_file(ADMIN_CREDENTIALS)) {
        return null;
    }
    $data = include ADMIN_CREDENTIALS;
    if (!is_array($data) || empty($data["username"]) || empty($data["password_hash"])) {
        return null;
    }
    return $data;
}

function admin_logged_in() {
    return !empty($_SESSION["admin_ok"]);
}

function admin_require_login() {
    if (!admin_credentials()) {
        header("Location: setup.php");
        exit;
    }
    if (!admin_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

function admin_csrf_token() {
    if (empty($_SESSION["csrf"])) {
        $_SESSION["csrf"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf"];
}

function admin_csrf_field() {
    return '<input type="hidden" name="csrf" value="' . admin_h(admin_csrf_token()) . '">';
}

function admin_csrf_check() {
    $token = $_POST["csrf"] ?? "";
    if (!is_string($token) || !hash_equals($_SESSION["csrf"] ?? "", $token)) {
        http_response_code(403);
        admin_flash("error", "Il modulo è scaduto. Riprova.");
        header("Location: " . admin_safe_return());
        exit;
    }
}

function admin_safe_return() {
    $fallback = "index.php";
    $target = $_POST["return"] ?? $_SERVER["HTTP_REFERER"] ?? $fallback;
    if (!is_string($target)) {
        return $fallback;
    }
    $path = parse_url($target, PHP_URL_PATH) ?? "";
    $file = basename($path);
    if (!preg_match("/^[a-z]+\.php$/", $file)) {
        return $fallback;
    }
    return $file;
}

function admin_flash($type, $message) {
    $_SESSION["flash"] = ["type" => $type, "message" => $message];
}

function admin_take_flash() {
    $flash = $_SESSION["flash"] ?? null;
    unset($_SESSION["flash"]);
    return is_array($flash) ? $flash : null;
}

function admin_write_credentials($username, $password) {
    $payload = var_export([
        "username" => $username,
        "password_hash" => password_hash($password, PASSWORD_DEFAULT),
    ], true);
    $ok = file_put_contents(ADMIN_CREDENTIALS, "<?php\nreturn {$payload};\n", LOCK_EX);
    if ($ok === false) {
        return false;
    }
    @chmod(ADMIN_CREDENTIALS, 0600);
    return true;
}

function admin_layout($title, $body, $options = []) {
    $flash = admin_take_flash();
    $nav = !empty($options["nav"]);
    $wide = !empty($options["wide"]);

    header("Content-Type: text/html; charset=utf-8");
    echo '<!doctype html><html lang="it"><head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<meta name="robots" content="noindex, nofollow">';
    echo '<title>' . admin_h($title) . " · Ginnastica Meda Admin</title>";
    echo '<link rel="icon" href="../images/logo/logo-50.png">';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Figtree:wght@400;500;600;700&display=swap" rel="stylesheet">';
    echo '<link rel="stylesheet" href="./admin.css">';
    echo "</head><body class=\"admin" . ($wide ? " admin--wide" : "") . '">';

    if ($nav) {
        echo '<header class="admin-bar">';
        echo '<a class="admin-bar__mark" href="index.php">Ginnastica Meda<span>admin</span></a>';
        echo '<nav class="admin-bar__nav">';
        echo '<a href="password.php">Password</a>';
        echo '<a href="../" target="_blank" rel="noopener">Vedi sito</a>';
        echo '<form method="post" action="logout.php">' . admin_csrf_field() . '<button type="submit">Esci</button></form>';
        echo "</nav></header>";
    }

    if (!empty($options["bare"])) {
        echo $body;
        echo "</body></html>";
        return;
    }

    echo '<main class="admin-main">';
    if ($flash) {
        echo '<p class="admin-flash admin-flash--' . admin_h($flash["type"]) . '" role="status">' . admin_h($flash["message"]) . "</p>";
    }
    echo $body;
    echo "</main>";
    echo '<script src="./admin.js" defer></script>';
    echo "</body></html>";
}
