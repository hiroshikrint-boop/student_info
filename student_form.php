<?php
require 'config.php';
need_admin();

$id = (int) ($_GET['id'] ?? 0);
$courses = get_courses($db);
$d = blank_student();

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM students WHERE id = ?');
    $stmt->execute([$id]);
    $d = $stmt->fetch() ?: redirect('students.php');
    $d['username'] = '';
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $d = read_student_post();

    if ($id === 0) {
        if (!($error = create_student($db, $d))) {
            flash('Student added.');
            redirect('students.php');
        }
    } else {
        $error = check_student_fields($d);
        if (!$error) {
            $stmt = $db->prepare('SELECT 1 FROM students WHERE (student_no = ? OR email = ?) AND id != ?');
            $stmt->execute([$d['student_no'], $d['email'], $id]);
            if ($stmt->fetch()) $error = 'Student number or email is already used by another student.';
        }
        if (!$error) {
            $db->prepare('UPDATE students SET student_no = ?, full_name = ?, email = ?, course_id = ?, year_level = ?, address = ? WHERE id = ?')
               ->execute([$d['student_no'], $d['full_name'], $d['email'], $d['course_id'], $d['year_level'], $d['address'], $id]);
            flash('Student updated.');
            redirect('students.php');
        }
    }
}

$page = $id ? 'students' : 'add'; $title = $id ? 'Edit Student' : 'Add Student';
include 'header.php';
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="pcnl-card">
            <p class="eyebrow">Registrar</p>
            <h1 class="h3 mb-4"><?= $title ?></h1>
            <?php if ($error): ?><div class="alert alert-danger pcnl-alert"><?= e($error) ?></div><?php endif; ?>
            <form method="post">
                <?= csrf_field() ?>
                <?php $show_account = ($id === 0); $confirm = false; include '_student_fields.php'; ?>
                <button type="submit" class="btn btn-pcnl">Save</button>
                <a href="students.php" class="btn btn-pcnl-outline">Cancel</a>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
