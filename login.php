<?php
require_once __DIR__ . '/config/config.php';

if (current_user()) redirect('index.php');

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $users = read_json(USERS_FILE);
    $found = null;
    foreach ($users as $u) {
        if (strtolower($u['email']) === $email) {
            $found = $u;
            break;
        }
    }

    if ($found && password_verify($password, $found['password'])) {
        $_SESSION['user_id'] = $found['id'];
        flash('success', 'Welcome back, ' . e($found['name']) . '.');
        redirect(!empty($found['is_admin']) ? 'admin/index.php' : 'index.php');
    } else {
        $errors[] = 'Invalid email or password.';
    }
}

$page_title = 'Login — ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<div class="form-card">
    <h2>Log in</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $err) echo '<div>' . e($err) . '</div>'; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="login.php">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-block">Log in</button>
        <div class="form-footer">No account? <a href="register.php">Register</a>.</div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
