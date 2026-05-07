<?php
// =============================================================
// Registrar Approve Student
// On approval:
//   1. Mark students.status = 'approved'
//   2. Create graduates record with unique Graduate ID
//   3. Auto-create alumni record bound to the same user
//   4. Promote user role to 'alumni'
// =============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_role(['registrar','admin']);

$student_id = (int)($_GET['id'] ?? 0);
if (!$student_id) { flash('error','Invalid student.'); redirect('pages/registrar/verify.php'); }

$student = db_select_one($conn, "SELECT * FROM students WHERE id=?", 'i', [$student_id]);
if (!$student) { flash('error','Student not found.'); redirect('pages/registrar/verify.php'); }

// Update status
db_execute($conn, "UPDATE students SET status='approved' WHERE id=?", 'i', [$student_id]);

// Check if graduate already exists
$existing = db_select_one($conn, "SELECT id FROM graduates WHERE student_id=?", 'i', [$student_id]);
if ($existing) {
    flash('success', 'Student already approved.');
    redirect('pages/registrar/verify.php');
}

// Create unique Graduate ID
do {
    $grad_id = generate_unique_id('GRAD-');
    $dup = db_select_one($conn, "SELECT id FROM graduates WHERE graduate_id=?", 's', [$grad_id]);
} while ($dup);

$grad_pk = db_execute($conn,
    "INSERT INTO graduates (student_id, graduate_id, course, department, academic_year, graduation_date, approved_by) VALUES (?,?,?,?,?,?,?)",
    'isssssi',
    [$student_id, $grad_id, $student['course'], $student['department'], $student['academic_year'],
     $student['expected_graduation'], current_user_id()]);

// Auto-create alumni linked to the same user
db_execute($conn,
    "INSERT INTO alumni (user_id, graduate_id, employment_status) VALUES (?,?,?)",
    'iis', [$student['user_id'], $grad_pk, 'Unemployed']);

// Promote user role to 'alumni'
db_execute($conn, "UPDATE users SET role='alumni' WHERE id=?", 'i', [$student['user_id']]);

flash('success', "Approved. Graduate ID: {$grad_id}. Alumni account auto-created.");
redirect('pages/registrar/verify.php');
