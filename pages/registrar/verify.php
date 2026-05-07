<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role(['registrar','admin']);

$filter = $_GET['status'] ?? 'pending';
$valid = ['pending','approved','rejected','all'];
if (!in_array($filter, $valid, true)) $filter = 'pending';
$where = $filter === 'all' ? '1' : "s.status='" . mysqli_real_escape_string($conn, $filter) . "'";
$students = db_select($conn, "SELECT s.*, u.full_name, u.email, u.contact FROM students s JOIN users u ON u.id=s.user_id WHERE $where ORDER BY s.created_at DESC");

$page_title = 'Verify Students';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">Student Verification</h1>
  <p class="text-sm text-gray-500">Approve or reject graduation applications.</p>

  <div class="mt-4 flex gap-2 flex-wrap">
    <?php foreach (['pending','approved','rejected','all'] as $f):
      $active = $f === $filter ? 'bg-crimson-700 text-white' : 'bg-white text-gray-700 border border-gray-300'; ?>
      <a href="?status=<?= $f ?>" class="px-3 py-1.5 rounded-md text-sm font-semibold <?= $active ?>"><?= ucfirst($f) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="mt-6 card overflow-x-auto">
    <table class="table-clean">
      <thead><tr><th>ID</th><th>Name</th><th>Course</th><th>Year</th><th>AY</th><th>Reqs</th><th>Paid</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        <?php foreach ($students as $s):
          $reqs_total = db_count($conn,'requirements','student_id=?','i',[$s['id']]);
          $reqs_ok = db_count($conn,'requirements',"student_id=? AND status='approved'",'i',[$s['id']]);
          $paid = db_select_one($conn,"SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE user_id=? AND status='paid'",'i',[$s['user_id']]);
        ?>
          <tr>
            <td class="font-mono text-xs"><?= e($s['student_id']) ?></td>
            <td>
              <div class="font-semibold"><?= e($s['full_name']) ?></div>
              <div class="text-xs text-gray-500"><?= e($s['email']) ?></div>
            </td>
            <td><?= e($s['course']) ?></td>
            <td><?= e($s['year_level']) ?></td>
            <td><?= e($s['academic_year']) ?></td>
            <td><span class="text-xs"><?= $reqs_ok ?>/<?= $reqs_total ?></span></td>
            <td class="text-xs"><?= fmt_money($paid['s']) ?></td>
            <td><?= status_badge($s['status']) ?></td>
            <td>
              <div class="flex gap-1 flex-wrap">
                <a href="<?= APP_URL ?>/pages/registrar/requirements.php?student_id=<?= (int)$s['id'] ?>" class="text-blue-700 text-xs hover:underline">View Reqs</a>
                <?php if ($s['status'] !== 'approved'): ?>
                  <a href="<?= APP_URL ?>/actions/registrar_approve_student.php?id=<?= (int)$s['id'] ?>"
                     data-confirm="Approve this student and create graduate + alumni records?"
                     class="text-emerald-700 text-xs font-semibold hover:underline">Approve</a>
                <?php endif; ?>
                <?php if ($s['status'] !== 'rejected'): ?>
                  <a href="<?= APP_URL ?>/actions/registrar_reject_student.php?id=<?= (int)$s['id'] ?>"
                     data-confirm="Reject this student application?"
                     class="text-rose-700 text-xs font-semibold hover:underline">Reject</a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($students)): ?><tr><td colspan="9" class="text-center text-gray-400 py-6">No records.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
