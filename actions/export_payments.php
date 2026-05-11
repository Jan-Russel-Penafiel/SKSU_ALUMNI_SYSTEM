<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('registrar');

$rows = db_select($conn, "SELECT p.reference_no, u.full_name, u.email, p.payment_type, p.amount, p.payment_method, p.status, p.paid_at
    FROM payments p JOIN users u ON u.id=p.user_id ORDER BY p.paid_at DESC");

send_xlsx(
    ['Reference','Payer','Email','Type','Amount','Method','Status','Date'],
    $rows,
    'payments_export_' . date('Ymd'),
    'Payments'
);
