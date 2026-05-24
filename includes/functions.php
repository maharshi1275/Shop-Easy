<?php

function read_json($file) {
    if (!file_exists($file)) {
        return [];
    }
    $fp = fopen($file, 'r');
    if (!$fp) return [];
    flock($fp, LOCK_SH);
    $contents = stream_get_contents($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    $data = json_decode($contents, true);
    return is_array($data) ? $data : [];
}

function write_json($file, $data) {
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $fp = fopen($file, 'c+');
    if (!$fp) return false;
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

function next_id(array $rows) {
    $max = 0;
    foreach ($rows as $r) {
        if (isset($r['id']) && $r['id'] > $max) $max = $r['id'];
    }
    return $max + 1;
}

function find_by_id(array $rows, $id) {
    foreach ($rows as $r) {
        if ((int)$r['id'] === (int)$id) return $r;
    }
    return null;
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function current_user() {
    if (empty($_SESSION['user_id'])) return null;
    $users = read_json(USERS_FILE);
    return find_by_id($users, $_SESSION['user_id']);
}

function require_login() {
    if (!current_user()) {
        redirect('login.php');
    }
}

function require_admin() {
    $u = current_user();
    if (!$u || empty($u['is_admin'])) {
        redirect('../login.php');
    }
}

function flash($key, $msg = null) {
    if ($msg === null) {
        $val = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $val;
    }
    $_SESSION['flash'][$key] = $msg;
}

function cart_items() {
    return $_SESSION['cart'] ?? [];
}

function cart_count() {
    $count = 0;
    foreach (cart_items() as $qty) $count += (int)$qty;
    return $count;
}

function cart_total() {
    $total = 0.0;
    $products = read_json(PRODUCTS_FILE);
    foreach (cart_items() as $pid => $qty) {
        $p = find_by_id($products, $pid);
        if ($p) $total += $p['price'] * $qty;
    }
    return $total;
}
