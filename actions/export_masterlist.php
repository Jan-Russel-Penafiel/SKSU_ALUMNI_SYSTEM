<?php
// Masterlist CSV Export
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role(['registrar','admin']);

$course = $_GET['course'] ?? '';
$year   = $_GET['year'] ?? '';
$dept   = $_GET['dept'] ?? '';
$conds = ['1']; $types=''; $params=[];
if ($course) { $conds[]='g.course=?'; $types.='s'; $params[]=$course; }
if ($year)   { $conds[]='g.academic_year=?'; $types.='s'; $params[]=$year; }
if ($dept)   { $conds[]='g.department=?'; $types.='s'; $params[]=$dept; }
$where = implode(' AND ', $conds);

$rows = db_select($conn, "SELECT g.graduate_id, u.full_name, u.email, g.course, g.department, g.academic_year, g.graduation_date, g.honors
    FROM graduates g JOIN students s ON s.id=g.student_id JOIN users u ON u.id=s.user_id
    WHERE $where ORDER BY g.course, u.full_name", $types, $params);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=graduate_masterlist_' . date('Ymd') . '.csv');
$out = fopen('php://output', 'w');
fputcsv($out, ['Graduate ID','Full Name','Email','Course','Department','Academic Year','Graduation Date','Honors']);
foreach ($rows as $r) {
    fputcsv($out, [$r['graduate_id'], $r['full_name'], $r['email'], $r['course'], $r['department'], $r['academic_year'], $r['graduation_date'], $r['honors']]);
}
fclose($out);
