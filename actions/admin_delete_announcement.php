<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

$id = (int)($_GET['id'] ?? 0);
if ($id) db_execute($conn, "DELETE FROM announcements WHERE id=?", 'i', [$id]);
flash('success','Announcement deleted.');
redirect('pages/admin/announcements.php');
