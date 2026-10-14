<?php
$page  = $page  ?? '';
$title = $title ?? 'Portal';
$logged = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · <?= APP_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-md pcnl-nav sticky-top">
    <div class="container">
        <a class="navbar-brand pcnl-brand" href="index.php">
            <svg width="38" height="38" viewBox="0 0 48 48" fill="none" aria-hidden="true">
                <path d="M24 3 42 10v13c0 11-7.6 19.2-18 22C13.600 42.200 6 34 6 23V10z" fill="#0b1220" stroke="#3b82f6" stroke-width="2"/>
                <path d="M15 31V17h8a4.500 4.500 0 0 1 0 9h-8M31 17v14h6" stroke="#fff" stroke-width="2.600" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span><strong>StudentInfo <em>PCNL</em></strong><small><?= APP_SCHOOL ?></small></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto align-items-md-center gap-md-1">
            <?php if ($logged): ?>
                <?php if (is_admin()): ?>
                    <li class="nav-item"><a class="nav-link <?= $page === 'students' ? 'active' : '' ?>" href="students.php">Students</a></li>
                    <li class="nav-item"><a class="nav-link <?= $page === 'add' ? 'active' : '' ?>" href="student_form.php">Add Student</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link <?= $page === 'profile' ? 'active' : '' ?>" href="profile.php">My Profile</a></li>
                <?php endif; ?>
                <li class="nav-item ms-md-2"><a class="btn btn-pcnl-outline btn-sm" href="logout.php">Logout · <?= e($_SESSION['username']) ?></a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link <?= $page === 'login' ? 'active' : '' ?>" href="login.php">Login</a></li>
                <li class="nav-item ms-md-2"><a class="btn btn-pcnl btn-sm" href="register.php">Register</a></li>
            <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-5">
<?= flash_show() ?>
