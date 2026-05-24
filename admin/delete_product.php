<?php
require_once __DIR__ . '/../config/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('products.php');
}

$id = (int)($_POST['id'] ?? 0);
$products = read_json(PRODUCTS_FILE);
$products = array_values(array_filter($products, fn($p) => (int)$p['id'] !== $id));
write_json(PRODUCTS_FILE, $products);

flash('success', 'Product deleted.');
redirect('products.php');
