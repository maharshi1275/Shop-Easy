<?php
require_once __DIR__ . '/config/config.php';
require_login();

$user  = current_user();
$items = cart_items();

if (empty($items)) {
    flash('error', 'Your cart is empty.');
    redirect('cart.php');
}

$products = read_json(PRODUCTS_FILE);
$errors   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $city    = trim($_POST['city'] ?? '');
    $zip     = trim($_POST['zip'] ?? '');
    $payment = $_POST['payment'] ?? '';

    if ($address === '') $errors[] = 'Shipping address is required.';
    if ($city === '')    $errors[] = 'City is required.';
    if ($zip === '')     $errors[] = 'Zip / postal code is required.';
    if (!in_array($payment, ['cod', 'card'], true)) $errors[] = 'Choose a payment method.';

    $order_items = [];
    $total = 0.0;
    foreach ($items as $pid => $qty) {
        $p = find_by_id($products, $pid);
        if (!$p) continue;
        $qty = min((int)$qty, (int)$p['stock']);
        if ($qty <= 0) continue;
        $line = [
            'product_id' => (int)$p['id'],
            'name'       => $p['name'],
            'price'      => (float)$p['price'],
            'qty'        => $qty,
        ];
        $order_items[] = $line;
        $total += $p['price'] * $qty;
    }

    if (empty($order_items)) $errors[] = 'No valid items in your cart.';

    if (empty($errors)) {
        $orders = read_json(ORDERS_FILE);
        $order = [
            'id'         => next_id($orders),
            'user_id'    => (int)$user['id'],
            'items'      => $order_items,
            'total'      => round($total, 2),
            'address'    => $address,
            'city'       => $city,
            'zip'        => $zip,
            'payment'    => $payment,
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $orders[] = $order;
        write_json(ORDERS_FILE, $orders);

        foreach ($order_items as $line) {
            foreach ($products as &$p) {
                if ((int)$p['id'] === (int)$line['product_id']) {
                    $p['stock'] = max(0, (int)$p['stock'] - (int)$line['qty']);
                }
            }
            unset($p);
        }
        write_json(PRODUCTS_FILE, $products);

        $_SESSION['cart'] = [];
        flash('success', 'Order #' . $order['id'] . ' placed successfully.');
        redirect('orders.php');
    }
}

$page_title = 'Checkout — ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">Checkout</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $err) echo '<div>' . e($err) . '</div>'; ?>
    </div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div class="form-card" style="margin:0;max-width:none;">
        <h2>Shipping &amp; Payment</h2>
        <form method="post" action="checkout.php">
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?= e($_POST['address'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" value="<?= e($_POST['city'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="zip">Zip / Postal Code</label>
                <input type="text" id="zip" name="zip" value="<?= e($_POST['zip'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="payment">Payment method</label>
                <select id="payment" name="payment" required>
                    <option value="">-- Select --</option>
                    <option value="cod" <?= ($_POST['payment'] ?? '') === 'cod' ? 'selected' : '' ?>>Cash on delivery</option>
                    <option value="card" <?= ($_POST['payment'] ?? '') === 'card' ? 'selected' : '' ?>>Credit / debit card (demo)</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success btn-block">Place order</button>
        </form>
    </div>

    <div class="form-card" style="margin:0;max-width:none;">
        <h2>Order summary</h2>
        <table class="table" style="border:none;">
            <?php foreach ($items as $pid => $qty):
                $p = find_by_id($products, $pid);
                if (!$p) continue;
            ?>
                <tr>
                    <td><?= e($p['name']) ?> &times; <?= (int)$qty ?></td>
                    <td style="text-align:right;">$<?= number_format($p['price'] * $qty, 2) ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th>Total</th>
                <th style="text-align:right;">$<?= number_format(cart_total(), 2) ?></th>
            </tr>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
