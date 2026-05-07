<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('pages/admin/users.php');
$u = db_select_one($conn, "SELECT status FROM users WHERE id=?", 'i', [$id]);
if (!$u) redirect('pages/admin/users.php');
$new = $u['status'] === 'active' ? 'inactive' : 'active';
db_execute($conn, "UPDATE users SET status=? WHERE id=?", 'si', [$new, $id]);
flash('success', "User status set to {$new}.");
redirect('pages/admin/users.php');
