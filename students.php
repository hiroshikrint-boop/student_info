<?php
require 'config.php';
need_admin();

$course  = (int) ($_GET['course'] ?? 0);
$courses = get_courses($db);

$sql = 'SELECT s.*, c.code FROM students s JOIN courses c ON s.course_id = c.id';
$params = [];
if ($course > 0) { $sql .= ' WHERE s.course_id = ?'; $params[] = $course; }
$stmt = $db->prepare($sql . ' ORDER BY s.student_no');
$stmt->execute($params);
$students = $stmt->fetchAll();

$total = (int) $db->query('SELECT COUNT(*) FROM students')->fetchColumn();
$perCourse = $db->query('SELECT c.code, COUNT(s.id) n FROM courses c LEFT JOIN students s ON s.course_id = c.id GROUP BY c.id ORDER BY c.code')->fetchAll();

$page = 'students'; $title = 'Students';
include 'header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <p class="eyebrow">Registrar</p>
        <h1 class="h3 mb-0">Student Records</h1>
    </div>
    <a href="student_form.php" class="btn btn-pcnl">+ Add Student</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3"><div class="pcnl-card stat"><span>Total students</span><strong><?= $total ?></strong></div></div>
    <?php foreach ($perCourse as $pc): ?>
        <div class="col-6 col-lg-3"><div class="pcnl-card stat"><span><?= e($pc['code']) ?></span><strong><?= (int) $pc['n'] ?></strong></div></div>
    <?php endforeach; ?>
</div>

<form method="get" class="row g-2 mb-3">
    <div class="col-12 col-md-6"><input type="search" id="searchBox" class="form-control" placeholder="Search name, number or email…"></div>
    <div class="col-8 col-md-4">
        <select name="course" class="form-select">
            <option value="0">All courses</option>
            <?php foreach ($courses as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= $course == $c['id'] ? 'selected' : '' ?>><?= e($c['code']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-4 col-md-2"><button class="btn btn-pcnl-outline w-100">Filter</button></div>
</form>

<div class="pcnl-card p-0 overflow-hidden">
<div class="table-responsive">
<table class="table pcnl-table mb-0" id="studentTable">
    <thead><tr><th>Student No.</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th class="text-end">Actions</th></tr></thead>
    <tbody>
    <?php if (!$students): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No students found.</td></tr>
    <?php endif; ?>
    <?php foreach ($students as $s): ?>
        <tr>
            <td class="mono"><?= e($s['student_no']) ?></td>
            <td><?= e($s['full_name']) ?></td>
            <td class="text-muted"><?= e($s['email']) ?></td>
            <td><span class="badge-pcnl"><?= e($s['code']) ?></span></td>
            <td><?= (int) $s['year_level'] ?></td>
            <td class="text-end text-nowrap">
                <a href="student_form.php?id=<?= (int) $s['id'] ?>" class="btn btn-sm btn-pcnl-outline">Edit</a>
                <form method="post" action="student_delete.php" class="d-inline" data-confirm="Delete this student and their account?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
                    <button class="btn btn-sm btn-pcnl-danger">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
<?php include 'footer.php'; ?>
