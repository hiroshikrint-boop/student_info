<?php
// Deleting the user row also removes the student row (ON DELETE CASCADE).
require 'config.php';
need_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $db->prepare('DELETE FROM users WHERE id = (SELECT user_id FROM students WHERE id = ?)')
       ->execute([(int) ($_POST['id'] ?? 0)]);
    flash('Student deleted.');
}
redirect('students.php');
