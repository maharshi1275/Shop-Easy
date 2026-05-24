<?php
require_once __DIR__ . '/config/config.php';

$products = read_json(PRODUCTS_FILE);
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $needle = strtolower($q);
    $products = array_filter($products, function ($p) use ($needle) {
        return stripos($p['name'], $needle) !== false
            || stripos($p['description'], $needle) !== false;
    });
}

$page_title = SITE_NAME . ' — Online Shopping';
include __DIR__ . '/includes/header.php';
?>

<?php if ($q === ''): ?>
<section class="hero">
    <div class="hero-content">
        <span class="hero-eyebrow">New season · Free shipping over $50</span>
        <h1>Things you'll <span class="grad-text">actually love</span> to own.</h1>
        <p>Curated everyday essentials — headphones, watches, bags, and more — built to last and priced fairly.</p>
        <div class="hero-cta">
            <a href="#catalog" class="btn btn-lg">Shop the collection</a>
            <a href="register.php" class="btn btn-lg btn-ghost">Create account</a>
        </div>
    </div>
    <div class="hero-orb hero-orb-1"></div>
    <div class="hero-orb hero-orb-2"></div>
</section>
<?php endif; ?>

<div class="toolbar" id="catalog">
    <div>
        <h2 class="section-title"><?= $q ? 'Search results' : 'Featured products' ?></h2>
        <p class="section-sub"><?= $q ? 'for "' . e($q) . '"' : 'Hand-picked items, always in stock' ?></p>
    </div>
    <form method="get" action="index.php" class="search-bar">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <input type="text" name="q" placeholder="Search products..." value="<?= e($q) ?>">
        <button type="submit" class="btn btn-sm">Search</button>
    </form>
</div>

<?php if (empty($products)): ?>
    <div class="empty-state">No products found<?= $q ? ' for "' . e($q) . '"' : '' ?>.</div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <div class="product-card">
                <a class="thumb" href="product.php?id=<?= (int)$p['id'] ?>">
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?= e($p['image']) ?>" alt="<?= e($p['name']) ?>">
                    <?php endif; ?>
                </a>
                <div class="body">
                    <h3><a href="product.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a></h3>
                    <div class="price">$<?= number_format($p['price'], 2) ?></div>
                    <div class="stock"><?= (int)$p['stock'] > 0 ? (int)$p['stock'] . ' in stock' : 'Out of stock' ?></div>
                    <div class="actions">
                        <?php if ((int)$p['stock'] > 0): ?>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                                <button class="btn btn-sm btn-block" type="submit">Add to cart</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-sm btn-block btn-secondary" disabled>Sold out</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
