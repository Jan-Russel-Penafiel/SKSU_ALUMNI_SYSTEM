<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('student');

if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/student/payments.php'); }
$uid = current_user_id();
$type   = trim($_POST['payment_type'] ?? 'Other');
$amount = (float)($_POST['amount'] ?? 0);
$method = trim($_POST['payment_method'] ?? 'Cash');
$remarks= trim($_POST['remarks'] ?? '');

if (!app_is_valid_option($type, app_payment_type_options())) {
    flash('error','Invalid payment type.');
    redirect('pages/student/payments.php');
}
if (!app_is_valid_option($method, app_payment_method_options())) {
    flash('error','Invalid payment method.');
    redirect('pages/student/payments.php');
}
if ($amount <= 0) { flash('error','Amount must be greater than zero.'); redirect('pages/student/payments.php'); }
$ref = generate_reference();
$status = 'pending';

$saved = db_execute($conn,
    "INSERT INTO payments (user_id, payment_type, amount, reference_no, payment_method, status, remarks, paid_at) VALUES (?,?,?,?,?,?,?,NULL)",
    'isdssss', [$uid, $type, $amount, $ref, $method, $status, $remarks]);

if (!$saved) {
    flash('error','Payment could not be submitted. Run the payment status migration and try again.');
    redirect('pages/student/payments.php');
}

flash('success', "Payment submitted for registrar review. Reference: {$ref}");
redirect('pages/student/payments.php');
