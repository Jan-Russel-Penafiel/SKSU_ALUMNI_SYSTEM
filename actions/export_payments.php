<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

$rows = db_select($conn, "SELECT p.reference_no, u.full_name, u.email, p.payment_type, p.amount, p.payment_method, p.status, p.paid_at
    FROM payments p JOIN users u ON u.id=p.user_id ORDER BY p.paid_at DESC");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=payments_export_' . date('Ymd') . '.csv');
$out = fopen('php://output','w');
fputcsv($out, ['Reference','Payer','Email','Type','Amount','Method','Status','Date']);
foreach ($rows as $r) fputcsv($out, array_values($r));
fclose($out);
