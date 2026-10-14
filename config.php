<?php
declare(strict_types=1);
/**
 * StudentInfo PCNL — core bootstrap
 * Database connection, hardened session, and shared helpers.
 */
const APP_NAME  = 'StudentInfo PCNL';
const APP_SCHOOL = 'Philippine College of Northwestern Luzon';

$host = 'sql305.infinityfree.com';
$dbname = 'if0_43172667_student_info';
$dbuser = 'if0_43172667';
$dbpass = '6Fh4g1J8eVYpwhs';

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'secure' => !empty($_SERVER['HTTPS'])]);
session_start();

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed. Check config.php');
}

/* ---------- Output & routing helpers ---------- */

function e($text): string
{
    return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function is_admin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}

function need_login(): void
{
    if (!isset($_SESSION['user_id'])) redirect('login.php');
}

function need_admin(): void
{
    need_login();
    if (!is_admin()) {
        http_response_code(403);
        die('Access denied. Administrators only.');
    }
}

/* ---------- CSRF protection ---------- */

function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function csrf_verify(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['csrf'] ?? ''))) {
        http_response_code(419);
        die('Security token expired. Go back, refresh the page and try again.');
    }
}

/* ---------- Flash messages ---------- */

function flash(string $msg, string $type = 'success'): void
{
    $_SESSION['flash'] = [$type, $msg];
}

function flash_show(): string
{
    if (empty($_SESSION['flash'])) return '';
    [$type, $msg] = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return '<div class="alert alert-' . e($type) . ' pcnl-alert">' . e($msg) . '</div>';
}

/* ---------- Data helpers ---------- */

function get_courses(PDO $db): array
{
    return $db->query('SELECT * FROM courses ORDER BY code')->fetchAll();
}

function blank_student(): array
{
    return ['username' => '', 'student_no' => '', 'full_name' => '', 'email' => '',
            'course_id' => 0, 'year_level' => 1, 'address' => ''];
}

function read_student_post(): array
{
    return [
        'username'   => trim($_POST['username'] ?? ''),
        'password'   => $_POST['password'] ?? '',
        'student_no' => trim($_POST['student_no'] ?? ''),
        'full_name'  => trim($_POST['full_name'] ?? ''),
        'email'      => trim($_POST['email'] ?? ''),
        'course_id'  => (int) ($_POST['course_id'] ?? 0),
        'year_level' => (int) ($_POST['year_level'] ?? 0),
        'address'    => trim($_POST['address'] ?? ''),
    ];
}

/* ---------- Validation ---------- */

function check_student_fields(array $d): string
{
    $len = fn(string $s) => mb_strlen($s);
    if ($len($d['full_name']) < 3 || $len($d['full_name']) > 100) return 'Full name must be 3 to 100 characters.';
    if (!preg_match('/^[0-9]{4}-[0-9]{4}$/', $d['student_no']))   return 'Student number must look like 2026-0001.';
    if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL) || $len($d['email']) > 100) return 'Please enter a valid email.';
    if ($d['course_id'] < 1)                                      return 'Please choose a course.';
    if ($d['year_level'] < 1 || $d['year_level'] > 5)             return 'Year level must be from 1 to 5.';
    if ($len($d['address']) > 200)                                return 'Address is too long (max 200 characters).';
    return '';
}

/** Creates the login account and student record atomically. Returns '' on success. */
function create_student(PDO $db, array $d): string
{
    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $d['username'])) return 'Username must be 3 to 30 characters (letters, numbers, underscore).';
    if (strlen($d['password']) < 6)                           return 'Password must be at least 6 characters.';
    if ($error = check_student_fields($d))                    return $error;

    $stmt = $db->prepare('SELECT 1 FROM users WHERE username = ?');
    $stmt->execute([$d['username']]);
    if ($stmt->fetch()) return 'Username is already taken.';

    $stmt = $db->prepare('SELECT 1 FROM students WHERE student_no = ? OR email = ?');
    $stmt->execute([$d['student_no'], $d['email']]);
    if ($stmt->fetch()) return 'Student number or email is already registered.';

    try {
        $db->beginTransaction();
        $db->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'student')")
           ->execute([$d['username'], password_hash($d['password'], PASSWORD_DEFAULT)]);
        $db->prepare('INSERT INTO students (user_id, student_no, full_name, email, course_id, year_level, address) VALUES (?, ?, ?, ?, ?, ?, ?)')
           ->execute([$db->lastInsertId(), $d['student_no'], $d['full_name'], $d['email'], $d['course_id'], $d['year_level'], $d['address']]);
        $db->commit();
    } catch (PDOException $ex) {
        $db->rollBack();
        return 'Could not save the record. Please try again.';
    }
    return '';
}
