<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('alumni');

if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/alumni/profile.php'); }

$uid = current_user_id();
$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$contact   = trim($_POST['contact'] ?? '');
$address   = trim($_POST['address'] ?? '');
$password  = $_POST['password'] ?? '';

if ($password !== '') {
    $hash = password_hash($password, PASSWORD_BCRYPT);
    db_execute($conn, "UPDATE users SET full_name=?, email=?, contact=?, address=?, password=? WHERE id=?",
        'sssssi', [$full_name, $email, $contact, $address, $hash, $uid]);
} else {
    db_execute($conn, "UPDATE users SET full_name=?, email=?, contact=?, address=? WHERE id=?",
        'ssssi', [$full_name, $email, $contact, $address, $uid]);
}

db_execute($conn,
    "UPDATE alumni SET employment_status=?, company_name=?, job_title=?, industry=?, work_address=?, monthly_income=?, career_achievements=?, last_updated=NOW() WHERE user_id=?",
    'sssssdsi',
    [
        $_POST['employment_status'] ?? 'Unemployed',
        $_POST['company_name'] ?? '',
        $_POST['job_title'] ?? '',
        $_POST['industry'] ?? '',
        $_POST['work_address'] ?? '',
        (float)($_POST['monthly_income'] ?? 0),
        $_POST['career_achievements'] ?? '',
        $uid
    ]);

$_SESSION['full_name'] = $full_name;
flash('success','Profile updated.');
redirect('pages/alumni/profile.php');
