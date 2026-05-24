<?php
$admin_section = 'Edit product';
require_once __DIR__ . '/../config/config.php';
require_admin();

$id       = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$products = read_json(PRODUCTS_FILE);
$idx      = null;
foreach ($products as $i => $p) {
    if ((int)$p['id'] === $id) { $idx = $i; break; }
}

if ($idx === null) {
    flash('error', 'Product not found.');
    redirect('products.php');
}

$errors = [];
$product = $products[$idx];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = $_POST['price'] ?? '';
    $stock       = $_POST['stock'] ?? '';
    $image       = trim($_POST['image'] ?? '');

    if ($name === '')                           $errors[] = 'Name is required.';
    if (!is_numeric($price) || $price < 0)      $errors[] = 'Price must be a non-negative number.';
    if (!is_numeric($stock) || (int)$stock < 0) $errors[] = 'Stock must be a non-negative integer.';

    if (!empty($_FILES['image_file']['name'])) {
        $f = $_FILES['image_file'];
        if ($f['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $allowed, true)) {
                $errors[] = 'Image must be jpg, png, gif, or webp.';
            } else {
                $fname = 'p_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest  = UPLOADS_PATH . '/' . $fname;
                if (move_uploaded_file($f['tmp_name'], $dest)) {
                    $image = 'uploads/' . $fname;
                } else {
                    $errors[] = 'Failed to save uploaded image.';
                }
            }
        } elseif ($f['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Image upload error.';
        }
    }

    if (empty($errors)) {
        $products[$idx]['name']        = $name;
        $products[$idx]['description'] = $description;
        $products[$idx]['price']       = round((float)$price, 2);
        $products[$idx]['stock']       = (int)$stock;
        $products[$idx]['image']       = $image;
        write_json(PRODUCTS_FILE, $products);
        flash('success', 'Product updated.');
        redirect('products.php');
    }

    $product = array_merge($product, [
        'name' => $name, 'description' => $description,
        'price' => $price, 'stock' => $stock, 'image' => $image,
    ]);
}

include __DIR__ . '/_layout.php';
?>

<h1 class="page-title">Edit product</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $err) echo '<div>' . e($err) . '</div>'; ?>
    </div>
<?php endif; ?>

<form method="post" action="edit_product.php" enctype="multipart/form-data" style="max-width:600px;">
    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= e($product['name']) ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= e($product['description']) ?></textarea>
    </div>
    <div class="form-group">
        <label for="price">Price ($)</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="<?= e($product['price']) ?>" required>
    </div>
    <div class="form-group">
        <label for="stock">Stock</label>
        <input type="number" id="stock" name="stock" min="0" value="<?= e($product['stock']) ?>" required>
    </div>
    <div class="form-group">
        <label for="image">Image URL or path</label>
        <input type="text" id="image" name="image" value="<?= e($product['image']) ?>">
        <?php if (!empty($product['image'])): ?>
            <div style="margin-top:8px;">
                <img src="<?= e((str_starts_with($product['image'], 'http') ? '' : '../') . $product['image']) ?>"
                     style="max-width:120px;border-radius:6px;border:1px solid #e5e7eb;">
            </div>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label for="image_file">Replace image (upload)</label>
        <input type="file" id="image_file" name="image_file" accept="image/*">
    </div>
    <button type="submit" class="btn">Save changes</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include __DIR__ . '/_layout_end.php'; ?>
