<?php
require_once __DIR__ . '/config/config.php';

$id = (int)($_GET['id'] ?? 0);
$products = read_json(PRODUCTS_FILE);
$product = find_by_id($products, $id);

if (!$product) {
    $page_title = 'Product not found';
    include __DIR__ . '/includes/header.php';
    echo '<div class="empty-state">Product not found.</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$page_title = $product['name'] . ' — ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<div class="product-detail">
    <div class="image">
        <?php if (!empty($product['image'])): ?>
            <img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>">
        <?php endif; ?>
    </div>
    <div>
        <h1><?= e($product['name']) ?></h1>
        <div class="price">$<?= number_format($product['price'], 2) ?></div>
        <p class="description"><?= nl2br(e($product['description'])) ?></p>
        <p class="stock">
            <?= (int)$product['stock'] > 0
                ? '<span class="badge badge-success">' . (int)$product['stock'] . ' in stock</span>'
                : '<span class="badge">Out of stock</span>' ?>
        </p>

        <?php if ((int)$product['stock'] > 0): ?>
            <form method="post" action="cart.php" style="margin-top:18px;display:flex;gap:10px;align-items:center;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <label for="qty">Qty:</label>
                <input type="number" id="qty" name="qty" value="1" min="1" max="<?= (int)$product['stock'] ?>"
                       style="width:80px;padding:8px;border:1px solid #d1d5db;border-radius:6px;">
                <button type="submit" class="btn">Add to cart</button>
            </form>
        <?php endif; ?>

        <p style="margin-top:18px;"><a href="index.php">&larr; Back to shop</a></p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
