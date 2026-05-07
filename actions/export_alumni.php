<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

$rows = db_select($conn, "SELECT g.graduate_id, u.full_name, u.email, u.contact, g.course, g.academic_year,
    a.employment_status, a.company_name, a.job_title, a.industry, a.work_address, a.monthly_income, a.last_updated
    FROM alumni a JOIN users u ON u.id=a.user_id JOIN graduates g ON g.id=a.graduate_id ORDER BY u.full_name");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=alumni_export_' . date('Ymd') . '.csv');
$out = fopen('php://output','w');
fputcsv($out, ['Graduate ID','Name','Email','Contact','Course','AY','Status','Company','Job','Industry','Work Address','Monthly Income','Last Updated']);
foreach ($rows as $r) {
    fputcsv($out, array_values($r));
}
fclose($out);
