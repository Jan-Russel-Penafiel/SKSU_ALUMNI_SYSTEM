<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

$id = (int)($_GET['id'] ?? 0);
if ($id) db_execute($conn, "DELETE FROM events WHERE id=?", 'i', [$id]);
flash('success','Event deleted.');
redirect('pages/admin/events.php');
