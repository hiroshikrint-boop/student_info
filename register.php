<?php
require 'config.php';
if (isset($_SESSION['user_id'])) redirect('index.php');

$error = '';
$courses = get_courses($db);
$d = blank_student();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $d = read_student_post();
    if ($d['password'] !== ($_POST['confirm'] ?? '')) {
        $error = 'Passwords do not match.';
    } elseif (!($error = create_student($db, $d))) {
        redirect('login.php?registered=1');
    }
}

$page = 'register'; $title = 'Registration';
include 'header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="pcnl-card">
            <p class="eyebrow">Enrollment</p>
            <h1 class="h3 mb-4">Student Registration</h1>
            <?php if ($error): ?><div class="alert alert-danger pcnl-alert"><?= e($error) ?></div><?php endif; ?>
            <form method="post" id="registerForm">
                <?= csrf_field() ?>
                <?php $show_account = true; $confirm = true; include '_student_fields.php'; ?>
                <button type="submit" class="btn btn-pcnl w-100">Create account</button>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
