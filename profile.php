<?php
require 'config.php';
need_login();
if (is_admin()) redirect('students.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $email   = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
        $error = 'Please enter a valid email.';
    } elseif (mb_strlen($address) > 200) {
        $error = 'Address is too long (max 200 characters).';
    } else {
        $stmt = $db->prepare('SELECT 1 FROM students WHERE email = ? AND user_id != ?');
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error = 'That email is used by another student.';
        } else {
            $db->prepare('UPDATE students SET email = ?, address = ? WHERE user_id = ?')
               ->execute([$email, $address, $_SESSION['user_id']]);
            flash('Profile updated.');
            redirect('profile.php');
        }
    }
}

$stmt = $db->prepare('SELECT s.*, c.code, c.name AS course_name FROM students s JOIN courses c ON s.course_id = c.id WHERE s.user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$s = $stmt->fetch() ?: redirect('logout.php');
$initials = strtoupper(mb_substr($s['full_name'], 0, 1));

$page = 'profile'; $title = 'My Profile';
include 'header.php';
?>
<div class="row g-4 justify-content-center">
    <div class="col-lg-5">
        <div class="pcnl-card id-card">
            <div class="id-top"><span>PCNL · Student ID</span><span class="dot"></span></div>
            <div class="avatar"><?= e($initials) ?></div>
            <h2 class="h4 mt-3 mb-0"><?= e($s['full_name']) ?></h2>
            <p class="id-no"><?= e($s['student_no']) ?></p>
            <dl class="id-list">
                <dt>Course</dt><dd><?= e($s['code']) ?> — <?= e($s['course_name']) ?></dd>
                <dt>Year Level</dt><dd>Year <?= (int) $s['year_level'] ?></dd>
            </dl>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="pcnl-card">
            <p class="eyebrow">Contact</p>
            <h2 class="h4 mb-4">Update contact info</h2>
            <?php if ($error): ?><div class="alert alert-danger pcnl-alert"><?= e($error) ?></div><?php endif; ?>
            <form method="post">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($s['email']) ?>" required maxlength="100">
                </div>
                <div class="mb-4">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="<?= e($s['address']) ?>" maxlength="200">
                </div>
                <button type="submit" class="btn btn-pcnl">Save changes</button>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
