<?php
$admin_section = 'Add product';
require_once __DIR__ . '/../config/config.php';
require_admin();

$errors = [];
$name = $description = $image = '';
$price = $stock = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = $_POST['price'] ?? '';
    $stock       = $_POST['stock'] ?? '';
    $image       = trim($_POST['image'] ?? '');

    if ($name === '')                          $errors[] = 'Name is required.';
    if (!is_numeric($price) || $price < 0)     $errors[] = 'Price must be a non-negative number.';
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
        $products = read_json(PRODUCTS_FILE);
        $products[] = [
            'id'          => next_id($products),
            'name'        => $name,
            'description' => $description,
            'price'       => round((float)$price, 2),
            'stock'       => (int)$stock,
            'image'       => $image,
            'created_at'  => date('Y-m-d H:i:s'),
        ];
        write_json(PRODUCTS_FILE, $products);
        flash('success', 'Product added.');
        redirect('products.php');
    }
}

include __DIR__ . '/_layout.php';
?>

<h1 class="page-title">Add product</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $err) echo '<div>' . e($err) . '</div>'; ?>
    </div>
<?php endif; ?>

<form method="post" action="add_product.php" enctype="multipart/form-data" style="max-width:600px;">
    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= e($name) ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description"><?= e($description) ?></textarea>
    </div>
    <div class="form-group">
        <label for="price">Price ($)</label>
        <input type="number" id="price" name="price" step="0.01" min="0" value="<?= e($price) ?>" required>
    </div>
    <div class="form-group">
        <label for="stock">Stock</label>
        <input type="number" id="stock" name="stock" min="0" value="<?= e($stock) ?>" required>
    </div>
    <div class="form-group">
        <label for="image">Image URL <span style="color:#6b7280;font-weight:normal;">(or upload below)</span></label>
        <input type="text" id="image" name="image" value="<?= e($image) ?>" placeholder="https://...">
    </div>
    <div class="form-group">
        <label for="image_file">Upload image</label>
        <input type="file" id="image_file" name="image_file" accept="image/*">
    </div>
    <button type="submit" class="btn">Save product</button>
    <a href="products.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include __DIR__ . '/_layout_end.php'; ?>
