<?php
$admin_section = 'Dashboard';
include __DIR__ . '/_layout.php';

$products = read_json(PRODUCTS_FILE);
$orders   = read_json(ORDERS_FILE);
$users    = read_json(USERS_FILE);
$revenue  = array_sum(array_column($orders, 'total'));
?>

<h1 class="page-title">Dashboard</h1>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
    <div style="padding:18px;background:#eff6ff;border-radius:8px;">
        <div style="color:#1d4ed8;font-size:0.85rem;">Products</div>
        <div style="font-size:1.8rem;font-weight:700;"><?= count($products) ?></div>
    </div>
    <div style="padding:18px;background:#ecfdf5;border-radius:8px;">
        <div style="color:#047857;font-size:0.85rem;">Orders</div>
        <div style="font-size:1.8rem;font-weight:700;"><?= count($orders) ?></div>
    </div>
    <div style="padding:18px;background:#fffbeb;border-radius:8px;">
        <div style="color:#92400e;font-size:0.85rem;">Users</div>
        <div style="font-size:1.8rem;font-weight:700;"><?= count($users) ?></div>
    </div>
    <div style="padding:18px;background:#fef2f2;border-radius:8px;">
        <div style="color:#991b1b;font-size:0.85rem;">Revenue</div>
        <div style="font-size:1.8rem;font-weight:700;">$<?= number_format($revenue, 2) ?></div>
    </div>
</div>

<h2 style="margin-top:30px;margin-bottom:14px;">Recent orders</h2>
<?php
usort($orders, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
$recent = array_slice($orders, 0, 5);
?>
<?php if (empty($recent)): ?>
    <div class="empty-state">No orders yet.</div>
<?php else: ?>
    <table class="table">
        <thead><tr><th>#</th><th>Customer</th><th>Total</th><th>Status</th><th>Placed</th></tr></thead>
        <tbody>
            <?php foreach ($recent as $o):
                $u = find_by_id($users, $o['user_id']);
            ?>
                <tr>
                    <td>#<?= (int)$o['id'] ?></td>
                    <td><?= e($u['name'] ?? 'Unknown') ?></td>
                    <td>$<?= number_format($o['total'], 2) ?></td>
                    <td><span class="badge badge-pending"><?= e($o['status']) ?></span></td>
                    <td><?= e($o['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/_layout_end.php'; ?>
