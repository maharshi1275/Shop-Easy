<?php
if (!defined('BASE_PATH')) {
    require_once __DIR__ . '/../config/config.php';
}
$user = current_user();
$base = $base ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? SITE_NAME) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="<?= $base ?>index.php" class="brand">
            <span class="brand-mark"></span>
            <span><?= e(SITE_NAME) ?></span>
        </a>
        <nav class="main-nav">
            <a href="<?= $base ?>index.php">Shop</a>
            <a href="<?= $base ?>cart.php" class="cart-link">
                Cart
                <?php if (cart_count() > 0): ?><span class="cart-badge"><?= cart_count() ?></span><?php endif; ?>
            </a>
            <?php if ($user): ?>
                <a href="<?= $base ?>orders.php">My Orders</a>
                <?php if (!empty($user['is_admin'])): ?>
                    <a href="<?= $base ?>admin/index.php">Admin</a>
                <?php endif; ?>
                <span class="nav-user">Hi, <?= e($user['name']) ?></span>
                <a href="<?= $base ?>logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= $base ?>login.php">Login</a>
                <a href="<?= $base ?>register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container main-content">
<?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= e($msg) ?></div>
<?php endif; ?>
<?php if ($msg = flash('error')): ?>
    <div class="alert alert-error"><?= e($msg) ?></div>
<?php endif; ?>
