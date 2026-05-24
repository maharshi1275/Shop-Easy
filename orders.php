<?php
require_once __DIR__ . '/config/config.php';
require_login();

$user   = current_user();
$orders = read_json(ORDERS_FILE);
$mine   = array_values(array_filter($orders, fn($o) => (int)$o['user_id'] === (int)$user['id']));
usort($mine, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

$page_title = 'My Orders — ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">My Orders</h1>

<?php if (empty($mine)): ?>
    <div class="empty-state">You have no orders yet. <a href="index.php">Start shopping</a>.</div>
<?php else: ?>
    <?php foreach ($mine as $o): ?>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:18px;margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:10px;">
                <strong>Order #<?= (int)$o['id'] ?></strong>
                <span class="badge badge-pending"><?= e($o['status']) ?></span>
                <span style="color:#6b7280;font-size:0.9rem;"><?= e($o['created_at']) ?></span>
            </div>
            <table class="table" style="border:none;">
                <?php foreach ($o['items'] as $line): ?>
                    <tr>
                        <td><?= e($line['name']) ?> &times; <?= (int)$line['qty'] ?></td>
                        <td style="text-align:right;">$<?= number_format($line['price'] * $line['qty'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th>Total</th>
                    <th style="text-align:right;">$<?= number_format($o['total'], 2) ?></th>
                </tr>
            </table>
            <p style="color:#6b7280;font-size:0.9rem;margin-top:8px;">
                Ship to: <?= e($o['address']) ?>, <?= e($o['city']) ?> <?= e($o['zip']) ?>
                &nbsp;·&nbsp; Payment: <?= e($o['payment']) ?>
            </p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
