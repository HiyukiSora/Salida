<?php
require_once 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username/email or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>

<section class="auth-page">
    <form class="auth-form" method="POST">
        <h1>Welcome Back</h1>
        <p class="auth-subtitle">Log in to save your watchlist and reviews.</p>

        <?php if ($error): ?>
            <div class="form-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" placeholder="Enter your username or email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn btn-block">Log In</button>

        <p class="auth-link">Don't have an account? <a href="register.php">Sign up</a></p>
    </form>
</section>

<?php require_once 'includes/footer.php'; ?>