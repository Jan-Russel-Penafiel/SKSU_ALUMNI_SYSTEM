<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('pages/student/profile.php');
if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/student/profile.php'); }

$uid = current_user_id();
$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$contact   = trim($_POST['contact'] ?? '');
$address   = trim($_POST['address'] ?? '');
$password  = $_POST['password'] ?? '';

// Update users
if ($password !== '') {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    db_execute($conn, "UPDATE users SET full_name=?, email=?, contact=?, address=?, password=? WHERE id=?",
        'sssssi', [$full_name, $email, $contact, $address, $hash, $uid]);
} else {
    db_execute($conn, "UPDATE users SET full_name=?, email=?, contact=?, address=? WHERE id=?",
        'ssssi', [$full_name, $email, $contact, $address, $uid]);
}

// Update student academic info
db_execute($conn,
    "UPDATE students SET course=?, year_level=?, department=?, academic_year=?, expected_graduation=? WHERE user_id=?",
    'sssssi', [$_POST['course'] ?? '', $_POST['year_level'] ?? '', $_POST['department'] ?? '',
              $_POST['academic_year'] ?? '', $_POST['expected_graduation'] ?? null, $uid]);

$_SESSION['full_name'] = $full_name;
flash('success', 'Profile updated.');
redirect('pages/student/profile.php');
