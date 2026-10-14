<?php
/** Shared student form fields. Expects $d (values) and $courses. Optional: $show_account (bool), $confirm (bool). */
$show_account = $show_account ?? false;
$confirm = $confirm ?? false;
?>
<?php if ($show_account): ?>
<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= e($d['username']) ?>" required minlength="3" maxlength="30" autocomplete="username">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" required minlength="6" autocomplete="new-password">
    </div>
</div>
<?php endif; ?>
<div class="mb-3">
    <label class="form-label">Student Number</label>
    <input type="text" name="student_no" id="student_no" class="form-control" value="<?= e($d['student_no']) ?>" placeholder="2026-0001" pattern="[0-9]{4}-[0-9]{4}" required>
    <div class="form-text" id="studentNoMsg"></div>
</div>
<div class="mb-3">
    <label class="form-label">Full Name</label>
    <input type="text" name="full_name" class="form-control" value="<?= e($d['full_name']) ?>" required maxlength="100">
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="<?= e($d['email']) ?>" required maxlength="100">
</div>
<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Course</label>
        <select name="course_id" class="form-select" required>
            <option value="">Choose course</option>
            <?php foreach ($courses as $c): ?>
                <option value="<?= (int) $c['id'] ?>" <?= $d['course_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['code']) ?> — <?= e($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Year Level</label>
        <select name="year_level" class="form-select">
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?= $i ?>" <?= $d['year_level'] == $i ? 'selected' : '' ?>>Year <?= $i ?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Address <span class="text-muted">(optional)</span></label>
    <input type="text" name="address" class="form-control" value="<?= e($d['address']) ?>" maxlength="200">
</div>
<?php if ($confirm): ?>
<div class="mb-3">
    <label class="form-label">Confirm Password</label>
    <input type="password" name="confirm" id="confirm" class="form-control" required autocomplete="new-password">
</div>
<div class="text-danger mb-2" id="errorMsg"></div>
<?php endif; ?>
