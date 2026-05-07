<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role(['registrar','admin']);

$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$where = "1";
$types = '';
$params = [];
if ($student_id) {
    $where = "r.student_id=?";
    $types = 'i';
    $params = [$student_id];
}
$rows = db_select($conn, "SELECT r.*, u.full_name, s.student_id AS sid_no FROM requirements r JOIN students s ON s.id=r.student_id JOIN users u ON u.id=s.user_id WHERE $where ORDER BY r.uploaded_at DESC", $types, $params);

$page_title = 'Verify Requirements';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">Requirement Validation</h1>
  <p class="text-sm text-gray-500">Approve or reject student-submitted documents.</p>

  <div class="mt-6 card overflow-x-auto">
    <table class="table-clean">
      <thead><tr><th>Student</th><th>Type</th><th>Title</th><th>File</th><th>Status</th><th>Submitted</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td>
              <div class="font-semibold"><?= e($r['full_name']) ?></div>
              <div class="text-xs text-gray-500 font-mono"><?= e($r['sid_no']) ?></div>
            </td>
            <td><?= e($r['requirement_type']) ?></td>
            <td><?= e($r['title']) ?></td>
            <td><a target="_blank" class="text-crimson-700 hover:underline" href="<?= APP_URL ?>/assets/<?= e($r['file_path']) ?>">View File</a></td>
            <td><?= status_badge($r['status']) ?></td>
            <td class="text-xs"><?= fmt_datetime($r['uploaded_at']) ?></td>
            <td>
              <form action="<?= APP_URL ?>/actions/registrar_validate_requirement.php" method="post" class="flex gap-1 items-center">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <input type="text" name="remarks" placeholder="Remarks" class="input text-xs py-1 w-28">
                <button name="action" value="approve" title="Approve" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-2 py-1 rounded inline-flex"><?= icon('check','w-4 h-4') ?></button>
                <button name="action" value="reject" title="Reject" class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-2 py-1 rounded inline-flex"><?= icon('x','w-4 h-4') ?></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="7" class="text-center text-gray-400 py-6">No requirements found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
