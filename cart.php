<?php
require_once __DIR__ . '/config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $pid    = (int)($_POST['product_id'] ?? 0);
    $qty    = (int)($_POST['qty'] ?? 1);
    if ($qty < 1) $qty = 1;

    $products = read_json(PRODUCTS_FILE);
    $product  = find_by_id($products, $pid);
    $cart     = $_SESSION['cart'] ?? [];

    if ($action === 'add' && $product) {
        $current = $cart[$pid] ?? 0;
        $new     = min($current + $qty, (int)$product['stock']);
        $cart[$pid] = $new;
        flash('success', e($product['name']) . ' added to cart.');
    } elseif ($action === 'update' && $product) {
        if ($qty <= 0) {
            unset($cart[$pid]);
        } else {
            $cart[$pid] = min($qty, (int)$product['stock']);
        }
        flash('success', 'Cart updated.');
    } elseif ($action === 'remove') {
        unset($cart[$pid]);
        flash('success', 'Item removed from cart.');
    } elseif ($action === 'clear') {
        $cart = [];
        flash('success', 'Cart cleared.');
    }

    $_SESSION['cart'] = $cart;
    redirect('cart.php');
}

$products = read_json(PRODUCTS_FILE);
$items    = cart_items();

$page_title = 'Your Cart — ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">Your Cart</h1>

<?php if (empty($items)): ?>
    <div class="empty-state">
        Your cart is empty. <a href="index.php">Browse products</a> to get started.
    </div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $pid => $qty):
                $p = find_by_id($products, $pid);
                if (!$p) continue;
                $subtotal = $p['price'] * $qty;
            ?>
                <tr>
                    <td>
                        <a href="product.php?id=<?= (int)$p['id'] ?>"><?= e($p['name']) ?></a>
                    </td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td>
                        <form method="post" action="cart.php" class="qty-form">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                            <input type="number" name="qty" value="<?= (int)$qty ?>" min="0" max="<?= (int)$p['stock'] ?>">
                            <button type="submit" class="btn btn-sm btn-secondary">Update</button>
                        </form>
                    </td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                    <td>
                        <form method="post" action="cart.php" class="inline-form">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="cart-summary">
        <form method="post" action="cart.php" class="inline-form">
            <input type="hidden" name="action" value="clear">
            <button type="submit" class="btn btn-sm btn-secondary">Clear cart</button>
        </form>
        <div class="total">Total: $<?= number_format(cart_total(), 2) ?></div>
        <a href="checkout.php" class="btn btn-success">Proceed to checkout</a>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
