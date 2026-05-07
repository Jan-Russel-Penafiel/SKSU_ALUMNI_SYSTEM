<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role(['student','alumni']);

if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/student/schedules.php'); }
$uid = current_user_id();
$type     = $_POST['schedule_type'] ?? 'Photobooth';
$title    = trim($_POST['title'] ?? '');
$desc     = trim($_POST['description'] ?? '');
$date     = $_POST['scheduled_date'] ?? '';
$time     = $_POST['scheduled_time'] ?? '';
$location = trim($_POST['location'] ?? '');

if (!$title || !$date || !$time) { flash('error','All required fields must be filled.'); redirect('pages/student/schedules.php'); }

// Conflict check (same type + date + time)
$exists = db_select_one($conn, "SELECT id FROM schedules WHERE schedule_type=? AND scheduled_date=? AND scheduled_time=?",
    'sss', [$type, $date, $time]);
if ($exists) { flash('error','That slot is already booked. Please choose another time.'); redirect('pages/student/schedules.php'); }

db_execute($conn,
    "INSERT INTO schedules (user_id, schedule_type, title, description, scheduled_date, scheduled_time, location) VALUES (?,?,?,?,?,?,?)",
    'issssss', [$uid, $type, $title, $desc, $date, $time, $location]);

flash('success', 'Schedule booked successfully.');
redirect('pages/student/schedules.php');
