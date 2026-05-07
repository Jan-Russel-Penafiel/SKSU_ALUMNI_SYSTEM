<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/admin/events.php'); }

$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$date        = $_POST['event_date'] ?? '';
$time        = $_POST['event_time'] ?? '';
$location    = trim($_POST['location'] ?? '');
$capacity    = (int)($_POST['capacity'] ?? 0);

if (!$title || !$date || !$time) { flash('error','Required fields missing.'); redirect('pages/admin/events.php'); }

db_execute($conn,
    "INSERT INTO events (title, description, event_date, event_time, location, capacity, created_by) VALUES (?,?,?,?,?,?,?)",
    'sssssii', [$title, $description, $date, $time, $location, $capacity, current_user_id()]);
flash('success','Event created.');
redirect('pages/admin/events.php');
