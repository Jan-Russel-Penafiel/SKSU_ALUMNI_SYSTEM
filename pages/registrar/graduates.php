<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role(['registrar','admin']);

$all_rows = db_select($conn, "SELECT g.*, u.full_name, u.email FROM graduates g JOIN students s ON s.id=g.student_id JOIN users u ON u.id=s.user_id ORDER BY g.id ASC");
$pg = paginate($all_rows, 10);
$rows = $pg['rows'];

$page_title = 'Graduates';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Graduate Records</h1>
      <p class="subtitle">Auto-generated from approved students. Each receives a unique Graduate ID and an alumni account.</p>
    </div>
  </div>

  <div class="table-wrap has-pagination overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Graduate ID</th>
          <th>Name</th>
          <th>Course</th>
          <th>Department</th>
          <th>AY</th>
          <th>Graduation Date</th>
          <th class="pr-6">Honors</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="pl-6 font-mono text-xs font-bold text-crimson-700" data-label="Graduate ID"><?= e($r['graduate_id']) ?></td>
            <td data-label="Name">
              <div class="font-semibold text-ink-900"><?= e($r['full_name']) ?></div>
              <div class="text-xs text-ink-500"><?= e($r['email']) ?></div>
            </td>
            <td class="text-ink-700" data-label="Course"><?= e($r['course']) ?></td>
            <td class="text-ink-600" data-label="Department"><?= e($r['department']) ?></td>
            <td class="text-ink-600" data-label="AY"><?= e($r['academic_year']) ?></td>
            <td class="text-ink-600" data-label="Graduation Date"><?= fmt_date($r['graduation_date']) ?></td>
            <td class="pr-6 text-xs text-ink-600" data-label="Honors"><?= e($r['honors'] ?: '&mdash;') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7"><div class="empty-state">
            <div class="empty-icon"><?= icon('cap','w-5 h-5') ?></div>
            <div>No graduates yet.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
