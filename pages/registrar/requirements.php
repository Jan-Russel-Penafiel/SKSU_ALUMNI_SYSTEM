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
$all_rows = db_select($conn, "SELECT r.*, u.full_name, s.student_id AS sid_no FROM requirements r JOIN students s ON s.id=r.student_id JOIN users u ON u.id=s.user_id WHERE $where ORDER BY r.id ASC", $types, $params);
$pg = paginate($all_rows, 10);
$rows = $pg['rows'];

$page_title = 'Verify Requirements';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Requirement Validation</h1>
      <p class="subtitle">Approve or reject student-submitted documents.</p>
    </div>
  </div>

  <div class="table-wrap has-pagination overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Student</th>
          <th>Type</th>
          <th>Title</th>
          <th>File</th>
          <th>Status</th>
          <th>Submitted</th>
          <th class="pr-6 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="pl-6" data-label="Student">
              <div class="font-semibold text-ink-900"><?= e($r['full_name']) ?></div>
              <div class="text-xs text-ink-500 font-mono"><?= e($r['sid_no']) ?></div>
            </td>
            <td class="text-ink-700" data-label="Type"><?= e($r['requirement_type']) ?></td>
            <td class="text-ink-700" data-label="Title"><?= e($r['title']) ?></td>
            <td data-label="File"><a target="_blank" class="text-xs font-semibold text-crimson-700 hover:bg-crimson-50 px-2 py-1 rounded-md" href="<?= APP_URL ?>/assets/<?= e($r['file_path']) ?>">View file</a></td>
            <td data-label="Status"><?= status_badge($r['status']) ?></td>
            <td class="text-xs text-ink-500" data-label="Submitted"><?= fmt_datetime($r['uploaded_at']) ?></td>
            <td class="pr-6 text-right" data-label="Actions">
              <form action="<?= APP_URL ?>/actions/registrar_validate_requirement.php" method="post" class="inline-flex gap-1 items-center justify-end">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <input type="text" name="remarks" placeholder="Remarks" class="input text-xs py-1 w-28">
                <button name="action" value="approve" title="Approve" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-2 py-1.5 rounded-md inline-flex"><?= icon('check','w-4 h-4') ?></button>
                <button name="action" value="reject" title="Reject" class="bg-rose-600 hover:bg-rose-700 text-white font-semibold px-2 py-1.5 rounded-md inline-flex"><?= icon('x','w-4 h-4') ?></button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7"><div class="empty-state">
            <div class="empty-icon"><?= icon('folder','w-5 h-5') ?></div>
            <div>No requirements found.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
