<?php
require_once __DIR__ . '/../config/config.php';
require_admin();
$base = '../';
$page_title = ($admin_section ?? 'Admin') . ' — ' . SITE_NAME;
include __DIR__ . '/../includes/header.php';

$current = basename($_SERVER['SCRIPT_NAME']);
?>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="index.php"    class="<?= $current === 'index.php'    ? 'active' : '' ?>">Dashboard</a>
        <a href="products.php" class="<?= in_array($current, ['products.php','add_product.php','edit_product.php']) ? 'active' : '' ?>">Products</a>
        <a href="orders.php"   class="<?= $current === 'orders.php'   ? 'active' : '' ?>">Orders</a>
        <a href="../index.php">View site</a>
    </aside>
    <section class="admin-main">
