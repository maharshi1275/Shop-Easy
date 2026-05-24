<?php
$admin_section = 'Orders';
require_once __DIR__ . '/../config/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
    $id     = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    if (!in_array($status, ['pending','paid','shipped','completed','cancelled'], true)) {
        $status = 'pending';
    }
    $orders = read_json(ORDERS_FILE);
    foreach ($orders as &$o) {
        if ((int)$o['id'] === $id) { $o['status'] = $status; break; }
    }
    unset($o);
    write_json(ORDERS_FILE, $orders);
    flash('success', 'Order #' . $id . ' updated.');
    redirect('orders.php');
}

include __DIR__ . '/_layout.php';

$orders = read_json(ORDERS_FILE);
$users  = read_json(USERS_FILE);
usort($orders, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
?>

<h1 class="page-title">Orders</h1>

<?php if (empty($orders)): ?>
    <div class="empty-state">No orders yet.</div>
<?php else: ?>
    <?php foreach ($orders as $o):
        $u = find_by_id($users, $o['user_id']);
    ?>
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin-bottom:14px;">
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:8px;">
                <div>
                    <strong>Order #<?= (int)$o['id'] ?></strong>
                    &nbsp;·&nbsp; <?= e($u['name'] ?? 'Unknown') ?> (<?= e($u['email'] ?? '') ?>)
                </div>
                <div style="color:#6b7280;font-size:0.9rem;"><?= e($o['created_at']) ?></div>
            </div>
            <div style="font-size:0.92rem;color:#374151;margin-bottom:8px;">
                Ship to: <?= e($o['address']) ?>, <?= e($o['city']) ?> <?= e($o['zip']) ?>
                &nbsp;·&nbsp; Payment: <?= e($o['payment']) ?>
                &nbsp;·&nbsp; <strong>Total: $<?= number_format($o['total'], 2) ?></strong>
            </div>
            <table class="table" style="background:#fff;margin-bottom:10px;">
                <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
                <tbody>
                    <?php foreach ($o['items'] as $line): ?>
                        <tr>
                            <td><?= e($line['name']) ?></td>
                            <td><?= (int)$line['qty'] ?></td>
                            <td>$<?= number_format($line['price'], 2) ?></td>
                            <td>$<?= number_format($line['price'] * $line['qty'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form method="post" action="orders.php" style="display:flex;gap:8px;align-items:center;">
                <input type="hidden" name="action" value="status">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <label>Status:</label>
                <select name="status">
                    <?php foreach (['pending','paid','shipped','completed','cancelled'] as $s): ?>
                        <option value="<?= $s ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm">Update</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/_layout_end.php'; ?>
