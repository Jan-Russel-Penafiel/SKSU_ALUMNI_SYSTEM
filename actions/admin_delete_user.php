<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

$id = (int)($_GET['id'] ?? 0);
if (!$id || $id === current_user_id()) {
    flash('error','Cannot delete this user.'); redirect('pages/admin/users.php');
}
db_execute($conn, "DELETE FROM users WHERE id=?", 'i', [$id]);
flash('success','User deleted.');
redirect('pages/admin/users.php');
