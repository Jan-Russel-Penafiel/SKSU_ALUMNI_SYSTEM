<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role(['registrar','admin']);

$id = (int)($_GET['id'] ?? 0);
if (!$id) { flash('error','Invalid student.'); redirect('pages/registrar/verify.php'); }
db_execute($conn, "UPDATE students SET status='rejected' WHERE id=?", 'i', [$id]);
flash('success', 'Application rejected.');
redirect('pages/registrar/verify.php');
