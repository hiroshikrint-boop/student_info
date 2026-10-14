<?php
require 'config.php';
if (isset($_SESSION['user_id'])) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([trim($_POST['username'] ?? '')]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'] ?? '', $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role']     = $user['role'];
        redirect('index.php');
    }
    usleep(300000); // slows down password guessing
    $error = 'Wrong username or password.';
}

if (isset($_GET['registered'])) flash('Account created! Please sign in.');
$page = 'login'; $title = 'Sign in';
include 'header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="pcnl-card auth-card">
            <p class="eyebrow">Student Portal</p>
            <h1 class="h3 mb-1">Welcome back</h1>
            <p class="text-muted mb-4">Sign in to access your records.</p>
            <?php if ($error): ?><div class="alert alert-danger pcnl-alert"><?= e($error) ?></div><?php endif; ?>
            <form method="post" autocomplete="on">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus autocomplete="username">
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required autocomplete="current-password">
                </div>
                <button type="submit" class="btn btn-pcnl w-100">Sign in</button>
            </form>
            <p class="mt-4 mb-0 small text-muted">New student? <a href="register.php">Create an account</a></p>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
