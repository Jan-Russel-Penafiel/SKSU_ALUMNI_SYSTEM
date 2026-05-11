<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('registrar');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('pages/registrar/payments.php');
if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/registrar/payments.php'); }

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

if (!$id || !in_array($action, ['approve','disapprove'], true)) {
    flash('error','Invalid payment review request.');
    redirect('pages/registrar/payments.php');
}

$payment = db_select_one($conn, "SELECT id, status FROM payments WHERE id=?", 'i', [$id]);
if (!$payment) {
    flash('error','Payment record not found.');
    redirect('pages/registrar/payments.php');
}

if ($payment['status'] !== 'pending') {
    flash('error','Only pending payments can be reviewed.');
    redirect('pages/registrar/payments.php');
}

$status = $action === 'approve' ? 'paid' : 'rejected';
$paidAt = $status === 'paid' ? date('Y-m-d H:i:s') : null;

$updated = db_execute($conn, "UPDATE payments SET status=?, paid_at=? WHERE id=?", 'ssi', [$status, $paidAt, $id]);
if (!$updated) {
    flash('error','Payment status could not be updated. Run the payment status migration and try again.');
    redirect('pages/registrar/payments.php');
}

flash('success', $status === 'paid' ? 'Payment approved.' : 'Payment disapproved.');
redirect('pages/registrar/payments.php');
