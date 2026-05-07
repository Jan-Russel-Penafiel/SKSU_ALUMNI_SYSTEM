<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('admin');

$q = trim($_GET['q'] ?? '');
$conds = ['1']; $types = ''; $params = [];
if ($q) {
    $conds[] = '(u.full_name LIKE ? OR s.student_id LIKE ? OR s.course LIKE ?)';
    $types .= 'sss';
    $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%";
}
$where = implode(' AND ', $conds);
$all_rows = db_select($conn, "SELECT s.*, u.full_name, u.email FROM students s JOIN users u ON u.id=s.user_id WHERE $where ORDER BY s.id ASC", $types, $params);
$pg = paginate($all_rows, 10);
$rows = $pg['rows'];

$page_title = 'Students';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Students</h1>
      <p class="subtitle">Browse and search graduating student records.</p>
    </div>
  </div>

  <form class="card flex gap-3 flex-wrap items-end mb-6">
    <div class="flex-1 min-w-[220px]">
      <label class="label">Search</label>
      <input name="q" value="<?= e($q) ?>" class="input" placeholder="Name, student ID, or course">
    </div>
    <div class="flex gap-2">
      <button class="btn-primary">Search</button>
      <a href="?" class="btn-ghost">Reset</a>
    </div>
  </form>

  <div class="table-wrap has-pagination overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Student ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Course</th>
          <th>Year</th>
          <th>AY</th>
          <th>Status</th>
          <th class="pr-6">Registered</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="pl-6 font-mono text-xs text-ink-600" data-label="Student ID"><?= e($r['student_id']) ?></td>
            <td class="font-semibold text-ink-900" data-label="Name"><?= e($r['full_name']) ?></td>
            <td class="text-ink-600" data-label="Email"><?= e($r['email']) ?></td>
            <td class="text-ink-700" data-label="Course"><?= e($r['course']) ?></td>
            <td class="text-ink-600" data-label="Year"><?= e($r['year_level']) ?></td>
            <td class="text-ink-600" data-label="AY"><?= e($r['academic_year']) ?></td>
            <td data-label="Status"><?= status_badge($r['status']) ?></td>
            <td class="pr-6 text-xs text-ink-500" data-label="Registered"><?= fmt_datetime($r['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="8"><div class="empty-state">
            <div class="empty-icon"><?= icon('cap','w-5 h-5') ?></div>
            <div>No students found.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
