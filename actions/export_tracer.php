<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('admin');

$rows = db_select($conn, "SELECT t.report_year, t.quarter, u.full_name, g.graduate_id, g.course, t.employment_status, t.company_name, t.job_title, t.related_to_course, t.notes, t.submitted_at
    FROM tracer_reports t JOIN alumni a ON a.id=t.alumni_id JOIN users u ON u.id=a.user_id JOIN graduates g ON g.id=a.graduate_id
    ORDER BY t.report_year DESC, t.quarter DESC");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=tracer_reports_' . date('Ymd') . '.csv');
$out = fopen('php://output','w');
fputcsv($out, ['Year','Quarter','Alumnus','Graduate ID','Course','Status','Company','Job Title','Course-related','Notes','Submitted']);
foreach ($rows as $r) fputcsv($out, array_values($r));
fclose($out);
