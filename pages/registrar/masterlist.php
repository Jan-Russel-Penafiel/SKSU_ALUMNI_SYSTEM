<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role(['registrar','admin']);

$course = $_GET['course'] ?? '';
$year   = $_GET['year'] ?? '';
$dept   = $_GET['dept'] ?? '';

$conds = ['1'];
$types = '';
$params = [];
if ($course) { $conds[] = 'g.course = ?';        $types .= 's'; $params[] = $course; }
if ($year)   { $conds[] = 'g.academic_year = ?'; $types .= 's'; $params[] = $year; }
if ($dept)   { $conds[] = 'g.department = ?';    $types .= 's'; $params[] = $dept; }
$where = implode(' AND ', $conds);

$rows = db_select($conn,
    "SELECT g.*, u.full_name, u.email FROM graduates g
     JOIN students s ON s.id=g.student_id JOIN users u ON u.id=s.user_id
     WHERE $where ORDER BY g.course, u.full_name", $types, $params);

$courses = db_select($conn, "SELECT DISTINCT course FROM graduates WHERE course IS NOT NULL ORDER BY course");
$years   = db_select($conn, "SELECT DISTINCT academic_year FROM graduates WHERE academic_year IS NOT NULL ORDER BY academic_year DESC");
$depts   = db_select($conn, "SELECT DISTINCT department FROM graduates WHERE department IS NOT NULL ORDER BY department");

$page_title = 'Graduate Masterlist';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">Official Graduate Masterlist</h1>
  <p class="text-sm text-gray-500">Filter by course, academic year, or department.</p>

  <form class="mt-4 card grid sm:grid-cols-4 gap-3">
    <div><label class="label">Course</label>
      <select name="course" class="input"><option value="">All</option>
        <?php foreach ($courses as $c): ?><option <?= $course===$c['course']?'selected':'' ?>><?= e($c['course']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div><label class="label">Academic Year</label>
      <select name="year" class="input"><option value="">All</option>
        <?php foreach ($years as $y): ?><option <?= $year===$y['academic_year']?'selected':'' ?>><?= e($y['academic_year']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div><label class="label">Department</label>
      <select name="dept" class="input"><option value="">All</option>
        <?php foreach ($depts as $d): ?><option <?= $dept===$d['department']?'selected':'' ?>><?= e($d['department']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="flex items-end gap-2">
      <button class="btn-primary">Filter</button>
      <a href="<?= APP_URL ?>/actions/export_masterlist.php?<?= http_build_query(['course'=>$course,'year'=>$year,'dept'=>$dept]) ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg">Export CSV</a>
    </div>
  </form>

  <div class="mt-6 card overflow-x-auto">
    <table class="table-clean">
      <thead><tr><th>#</th><th>Graduate ID</th><th>Name</th><th>Course</th><th>Department</th><th>AY</th><th>Graduation Date</th></tr></thead>
      <tbody>
        <?php $i=0; foreach ($rows as $r): $i++; ?>
          <tr>
            <td><?= $i ?></td>
            <td class="font-mono text-xs"><?= e($r['graduate_id']) ?></td>
            <td><?= e($r['full_name']) ?></td>
            <td><?= e($r['course']) ?></td>
            <td><?= e($r['department']) ?></td>
            <td><?= e($r['academic_year']) ?></td>
            <td><?= fmt_date($r['graduation_date']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="7" class="text-center text-gray-400 py-6">No graduates match the filter.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
