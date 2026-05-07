<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('student');

if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/student/requirements.php'); }
$uid = current_user_id();
$student = db_select_one($conn, "SELECT * FROM students WHERE user_id=?", 'i', [$uid]);
if (!$student) { flash('error','Student record missing.'); redirect('pages/student/requirements.php'); }

$type  = $_POST['requirement_type'] ?? 'Other';
$title = trim($_POST['title'] ?? '');
if (!$title) { flash('error','Title is required.'); redirect('pages/student/requirements.php'); }

[$ok, $result] = handle_upload('file', 'requirements');
if (!$ok) { flash('error', $result); redirect('pages/student/requirements.php'); }

db_execute($conn,
    "INSERT INTO requirements (student_id, requirement_type, title, file_path) VALUES (?,?,?,?)",
    'isss', [$student['id'], $type, $title, $result]);

flash('success', 'Requirement uploaded and pending registrar review.');
redirect('pages/student/requirements.php');
