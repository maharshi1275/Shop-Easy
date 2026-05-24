<?php
require_once __DIR__ . '/config/config.php';

if (current_user()) redirect('index.php');

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim(strtolower($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';

    if ($name === '')                              $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid email is required.';
    if (strlen($password) < 6)                     $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm)                    $errors[] = 'Passwords do not match.';

    $users = read_json(USERS_FILE);
    if (empty($errors)) {
        foreach ($users as $u) {
            if (strtolower($u['email']) === $email) {
                $errors[] = 'An account with that email already exists.';
                break;
            }
        }
    }

    if (empty($errors)) {
        $new = [
            'id'         => next_id($users),
            'name'       => $name,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_DEFAULT),
            'is_admin'   => false,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $users[] = $new;
        write_json(USERS_FILE, $users);

        $_SESSION['user_id'] = $new['id'];
        flash('success', 'Welcome, ' . e($name) . '! Your account has been created.');
        redirect('index.php');
    }
}

$page_title = 'Register — ' . SITE_NAME;
include __DIR__ . '/includes/header.php';
?>

<div class="form-card">
    <h2>Create an account</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $err) echo '<div>' . e($err) . '</div>'; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="register.php">
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="<?= e($name) ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6">
        </div>
        <div class="form-group">
            <label for="confirm">Confirm password</label>
            <input type="password" id="confirm" name="confirm" required minlength="6">
        </div>
        <button type="submit" class="btn btn-block">Register</button>
        <div class="form-footer">Already have an account? <a href="login.php">Log in</a>.</div>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
