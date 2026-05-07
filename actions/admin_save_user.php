<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/admin/users.php'); }

$id        = (int)($_POST['id'] ?? 0);
$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$role      = $_POST['role'] ?? 'student';
$contact   = trim($_POST['contact'] ?? '');
$address   = trim($_POST['address'] ?? '');
$status    = $_POST['status'] ?? 'active';
$password  = $_POST['password'] ?? '';

if (!$full_name || !$email || !in_array($role, ['student','registrar','alumni','admin'], true)) {
    flash('error','Required fields missing or invalid.'); redirect('pages/admin/users.php');
}

if ($id) {
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        db_execute($conn,
            "UPDATE users SET full_name=?, email=?, role=?, contact=?, address=?, status=?, password=? WHERE id=?",
            'sssssssi', [$full_name, $email, $role, $contact, $address, $status, $hash, $id]);
    } else {
        db_execute($conn,
            "UPDATE users SET full_name=?, email=?, role=?, contact=?, address=?, status=? WHERE id=?",
            'ssssssi', [$full_name, $email, $role, $contact, $address, $status, $id]);
    }
    flash('success','User updated.');
} else {
    if ($password === '') { flash('error','Password required for new users.'); redirect('pages/admin/users.php'); }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    db_execute($conn,
        "INSERT INTO users (full_name, email, password, role, contact, address, status) VALUES (?,?,?,?,?,?,?)",
        'sssssss', [$full_name, $email, $hash, $role, $contact, $address, $status]);
    flash('success','User created.');
}
redirect('pages/admin/users.php');
