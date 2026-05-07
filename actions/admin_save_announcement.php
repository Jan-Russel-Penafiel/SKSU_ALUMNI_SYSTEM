<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/admin/announcements.php'); }

$title = trim($_POST['title'] ?? '');
$body  = trim($_POST['body'] ?? '');
$audience = $_POST['audience'] ?? 'all';
if (!in_array($audience, ['all','students','alumni','registrar'], true)) $audience = 'all';
if (!$title || !$body) { flash('error','Title and body required.'); redirect('pages/admin/announcements.php'); }

db_execute($conn, "INSERT INTO announcements (title, body, audience, posted_by) VALUES (?,?,?,?)",
    'sssi', [$title, $body, $audience, current_user_id()]);
flash('success','Announcement posted.');
redirect('pages/admin/announcements.php');
