<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role(['registrar','admin']);

$rows = db_select($conn, "SELECT g.*, u.full_name, u.email FROM graduates g JOIN students s ON s.id=g.student_id JOIN users u ON u.id=s.user_id ORDER BY g.approved_at DESC");

$page_title = 'Graduates';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">Graduate Records</h1>
  <p class="text-sm text-gray-500">Auto-generated from approved students. Each receives a unique Graduate ID and an alumni account.</p>

  <div class="mt-6 card overflow-x-auto">
    <table class="table-clean">
      <thead><tr><th>Graduate ID</th><th>Name</th><th>Course</th><th>Department</th><th>AY</th><th>Graduation Date</th><th>Honors</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="font-mono text-xs font-bold text-crimson-700"><?= e($r['graduate_id']) ?></td>
            <td>
              <div class="font-semibold"><?= e($r['full_name']) ?></div>
              <div class="text-xs text-gray-500"><?= e($r['email']) ?></div>
            </td>
            <td><?= e($r['course']) ?></td>
            <td><?= e($r['department']) ?></td>
            <td><?= e($r['academic_year']) ?></td>
            <td><?= fmt_date($r['graduation_date']) ?></td>
            <td class="text-xs"><?= e($r['honors'] ?: '—') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="7" class="text-center text-gray-400 py-6">No graduates yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
