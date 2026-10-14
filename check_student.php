<?php
// Called by script.js: tells whether a student number is valid and still available.
require 'config.php';
header('Content-Type: application/json');

$no = trim($_GET['student_no'] ?? '');
if (!preg_match('/^[0-9]{4}-[0-9]{4}$/', $no)) {
    echo json_encode(['valid' => false, 'available' => false]);
    exit;
}
$stmt = $db->prepare('SELECT 1 FROM students WHERE student_no = ?');
$stmt->execute([$no]);
echo json_encode(['valid' => true, 'available' => !$stmt->fetch()]);
