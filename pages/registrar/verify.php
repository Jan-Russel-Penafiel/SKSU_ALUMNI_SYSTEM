<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role(['registrar','admin']);

$filter = $_GET['status'] ?? 'pending';
$valid = ['pending','approved','rejected','all'];
if (!in_array($filter, $valid, true)) $filter = 'pending';
$where = $filter === 'all' ? '1' : "s.status='" . mysqli_real_escape_string($conn, $filter) . "'";
$all_students = db_select($conn, "SELECT s.*, u.full_name, u.email, u.contact FROM students s JOIN users u ON u.id=s.user_id WHERE $where ORDER BY s.id ASC");
$pg = paginate($all_students, 10);
$students = $pg['rows'];

$page_title = 'Verify Students';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Student Verification</h1>
      <p class="subtitle">Approve or reject graduation applications.</p>
    </div>
  </div>

  <div class="mb-4 flex gap-2 flex-wrap">
    <?php foreach (['pending','approved','rejected','all'] as $f):
      $active = $f === $filter; ?>
      <a href="?status=<?= $f ?>" class="px-3 py-1.5 rounded-md text-xs font-semibold transition <?= $active ? 'bg-crimson-700 text-white' : 'bg-white text-ink-700 border border-ink-200 hover:bg-ink-50' ?>"><?= ucfirst($f) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="table-wrap has-pagination overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">ID</th>
          <th>Name</th>
          <th>Course</th>
          <th>Year</th>
          <th>AY</th>
          <th>Reqs</th>
          <th>Paid</th>
          <th>Status</th>
          <th class="pr-6 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $s):
          $reqs_total = db_count($conn,'requirements','student_id=?','i',[$s['id']]);
          $reqs_ok = db_count($conn,'requirements',"student_id=? AND status='approved'",'i',[$s['id']]);
          $paid = db_select_one($conn,"SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE user_id=? AND status='paid'",'i',[$s['user_id']]);
        ?>
          <tr>
            <td class="pl-6 font-mono text-xs text-ink-600" data-label="ID"><?= e($s['student_id']) ?></td>
            <td data-label="Name">
              <div class="font-semibold text-ink-900"><?= e($s['full_name']) ?></div>
              <div class="text-xs text-ink-500"><?= e($s['email']) ?></div>
            </td>
            <td class="text-ink-700" data-label="Course"><?= e($s['course']) ?></td>
            <td class="text-ink-600" data-label="Year"><?= e($s['year_level']) ?></td>
            <td class="text-ink-600" data-label="AY"><?= e($s['academic_year']) ?></td>
            <td data-label="Reqs"><span class="text-xs text-ink-600"><?= $reqs_ok ?>/<?= $reqs_total ?></span></td>
            <td class="text-xs text-ink-700 font-medium" data-label="Paid"><?= fmt_money($paid['s']) ?></td>
            <td data-label="Status"><?= status_badge($s['status']) ?></td>
            <td class="pr-6 text-right" data-label="Actions">
              <div class="inline-flex gap-1 flex-wrap justify-end">
                <a href="<?= APP_URL ?>/pages/registrar/requirements.php?student_id=<?= (int)$s['id'] ?>" class="text-xs font-semibold text-sky-700 hover:bg-sky-50 px-2 py-1 rounded-md">View reqs</a>
                <?php if ($s['status'] !== 'approved'): ?>
                  <a href="<?= APP_URL ?>/actions/registrar_approve_student.php?id=<?= (int)$s['id'] ?>"
                     data-confirm="Approve this student and create graduate + alumni records?"
                     class="text-xs font-semibold text-emerald-700 hover:bg-emerald-50 px-2 py-1 rounded-md">Approve</a>
                <?php endif; ?>
                <?php if ($s['status'] !== 'rejected'): ?>
                  <a href="<?= APP_URL ?>/actions/registrar_reject_student.php?id=<?= (int)$s['id'] ?>"
                     data-confirm="Reject this student application?"
                     class="text-xs font-semibold text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-md">Reject</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($students)): ?>
          <tr><td colspan="9"><div class="empty-state">
            <div class="empty-icon"><?= icon('check-circle','w-5 h-5') ?></div>
            <div>No records match this filter.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
