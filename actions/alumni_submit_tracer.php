<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role('alumni');

if (!csrf_check($_POST['csrf'] ?? '')) { flash('error','Invalid form token.'); redirect('pages/alumni/tracer.php'); }
$uid = current_user_id();
$alumni = db_select_one($conn, "SELECT * FROM alumni WHERE user_id=?", 'i', [$uid]);
if (!$alumni) { flash('error','Alumni record missing.'); redirect('pages/alumni/tracer.php'); }

$year    = (int)($_POST['report_year'] ?? date('Y'));
$quarter = $_POST['quarter'] ?? 'Q1';
$status  = $_POST['employment_status'] ?? 'Unemployed';
$company = trim($_POST['company_name'] ?? '');
$job     = trim($_POST['job_title'] ?? '');
$related = $_POST['related_to_course'] ?? 'Yes';
$notes   = trim($_POST['notes'] ?? '');

db_execute($conn,
    "INSERT INTO tracer_reports (alumni_id, quarter, report_year, employment_status, company_name, job_title, related_to_course, notes) VALUES (?,?,?,?,?,?,?,?)",
    'isisssss',
    [$alumni['id'], $quarter, $year, $status, $company, $job, $related, $notes]);

// Sync employment status to alumni table for quick stats
db_execute($conn, "UPDATE alumni SET employment_status=?, company_name=?, job_title=?, last_updated=NOW() WHERE id=?",
    'sssi', [$status, $company, $job, $alumni['id']]);

flash('success', "Tracer report for {$quarter} {$year} submitted.");
redirect('pages/alumni/tracer.php');
