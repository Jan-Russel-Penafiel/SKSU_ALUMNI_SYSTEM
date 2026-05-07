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

$all_rows = db_select($conn,
    "SELECT g.*, u.full_name, u.email FROM graduates g
     JOIN students s ON s.id=g.student_id JOIN users u ON u.id=s.user_id
     WHERE $where ORDER BY g.course, u.full_name", $types, $params);
$pg = paginate($all_rows, 15);
$rows = $pg['rows'];

$courses = db_select($conn, "SELECT DISTINCT course FROM graduates WHERE course IS NOT NULL ORDER BY course");
$years   = db_select($conn, "SELECT DISTINCT academic_year FROM graduates WHERE academic_year IS NOT NULL ORDER BY academic_year DESC");
$depts   = db_select($conn, "SELECT DISTINCT department FROM graduates WHERE department IS NOT NULL ORDER BY department");

$page_title = 'Graduate Masterlist';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Official Graduate Masterlist</h1>
      <p class="subtitle">Filter by course, academic year, or department.</p>
    </div>
    <a href="<?= APP_URL ?>/actions/export_masterlist.php?<?= http_build_query(['course'=>$course,'year'=>$year,'dept'=>$dept]) ?>" class="btn-primary">
      <?= icon('document','w-4 h-4') ?>
      Export XLSX
    </a>
  </div>

  <form class="card grid sm:grid-cols-4 gap-3 mb-6 items-end">
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
    <div class="flex gap-2">
      <button class="btn-primary">Filter</button>
      <a href="?" class="btn-ghost">Reset</a>
    </div>
  </form>

  <div class="table-wrap has-pagination overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">#</th>
          <th>Graduate ID</th>
          <th>Name</th>
          <th>Course</th>
          <th>Department</th>
          <th>AY</th>
          <th class="pr-6">Graduation Date</th>
        </tr>
      </thead>
      <tbody>
        <?php $i = ($pg['page'] - 1) * $pg['per_page']; foreach ($rows as $r): $i++; ?>
          <tr>
            <td class="pl-6 text-ink-500" data-label="#"><?= $i ?></td>
            <td class="font-mono text-xs font-bold text-crimson-700" data-label="Graduate ID"><?= e($r['graduate_id']) ?></td>
            <td class="font-semibold text-ink-900" data-label="Name"><?= e($r['full_name']) ?></td>
            <td class="text-ink-700" data-label="Course"><?= e($r['course']) ?></td>
            <td class="text-ink-600" data-label="Department"><?= e($r['department']) ?></td>
            <td class="text-ink-600" data-label="AY"><?= e($r['academic_year']) ?></td>
            <td class="pr-6 text-ink-600" data-label="Graduation Date"><?= fmt_date($r['graduation_date']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7"><div class="empty-state">
            <div class="empty-icon"><?= icon('list','w-5 h-5') ?></div>
            <div>No graduates match the filter.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
