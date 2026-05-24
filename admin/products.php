<?php
$admin_section = 'Products';
include __DIR__ . '/_layout.php';

$products = read_json(PRODUCTS_FILE);
?>

<div class="toolbar">
    <h1 class="page-title">Products</h1>
    <a href="add_product.php" class="btn btn-success btn-sm">+ Add product</a>
</div>

<?php if (empty($products)): ?>
    <div class="empty-state">No products yet. <a href="add_product.php">Add the first one</a>.</div>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>#</th><th>Name</th><th>Price</th><th>Stock</th><th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= (int)$p['id'] ?></td>
                    <td><?= e($p['name']) ?></td>
                    <td>$<?= number_format($p['price'], 2) ?></td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td>
                        <a href="edit_product.php?id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-secondary">Edit</a>
                        <form method="post" action="delete_product.php" class="inline-form"
                              onsubmit="return confirm('Delete this product?');">
                            <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include __DIR__ . '/_layout_end.php'; ?>
