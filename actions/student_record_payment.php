<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('student');

if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/student/payments.php'); }
$uid = current_user_id();
$type   = $_POST['payment_type'] ?? 'Other';
$amount = (float)($_POST['amount'] ?? 0);
$method = $_POST['payment_method'] ?? 'Cash';
$remarks= trim($_POST['remarks'] ?? '');

if ($amount <= 0) { flash('error','Amount must be greater than zero.'); redirect('pages/student/payments.php'); }
$ref = generate_reference();

db_execute($conn,
    "INSERT INTO payments (user_id, payment_type, amount, reference_no, payment_method, remarks) VALUES (?,?,?,?,?,?)",
    'isdsss', [$uid, $type, $amount, $ref, $method, $remarks]);

flash('success', "Payment recorded. Reference: {$ref}");
redirect('pages/student/payments.php');
