<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role(['registrar','admin']);

$id      = (int)($_POST['id'] ?? 0);
$action  = $_POST['action'] ?? '';
$remarks = trim($_POST['remarks'] ?? '');
if (!$id || !in_array($action, ['approve','reject'], true)) {
    flash('error','Invalid action.'); redirect('pages/registrar/requirements.php');
}
$status = $action === 'approve' ? 'approved' : 'rejected';
db_execute($conn, "UPDATE requirements SET status=?, remarks=? WHERE id=?", 'ssi', [$status, $remarks, $id]);
flash('success', "Requirement marked as {$status}.");
redirect('pages/registrar/requirements.php');
