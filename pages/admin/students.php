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
$rows = db_select($conn, "SELECT s.*, u.full_name, u.email FROM students s JOIN users u ON u.id=s.user_id WHERE $where ORDER BY s.created_at DESC", $types, $params);

$page_title = 'Students';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">Students</h1>
  <form class="mt-4 flex gap-2"><input name="q" value="<?= e($q) ?>" class="input flex-1" placeholder="Search by name, ID, course"><button class="btn-primary">Search</button></form>
  <div class="mt-6 card overflow-x-auto">
    <table class="table-clean">
      <thead><tr><th>Student ID</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>AY</th><th>Status</th><th>Registered</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="font-mono text-xs"><?= e($r['student_id']) ?></td>
            <td class="font-semibold"><?= e($r['full_name']) ?></td>
            <td class="text-xs"><?= e($r['email']) ?></td>
            <td><?= e($r['course']) ?></td>
            <td><?= e($r['year_level']) ?></td>
            <td><?= e($r['academic_year']) ?></td>
            <td><?= status_badge($r['status']) ?></td>
            <td class="text-xs"><?= fmt_datetime($r['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="8" class="text-center text-gray-400 py-6">No students.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
