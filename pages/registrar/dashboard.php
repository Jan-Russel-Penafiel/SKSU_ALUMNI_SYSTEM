<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role(['registrar','admin']);

$pending_students = db_count($conn, 'students', "status='pending'");
$approved_students = db_count($conn, 'students', "status='approved'");
$pending_reqs = db_count($conn, 'requirements', "status='pending'");
$total_grads = db_count($conn, 'graduates');

$recent = db_select($conn, "SELECT s.*, u.full_name, u.email FROM students s JOIN users u ON u.id=s.user_id ORDER BY s.created_at DESC LIMIT 10");

$page_title = 'Registrar Dashboard';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Registrar Dashboard</h1>
      <p class="subtitle">Verify and validate student records and graduation requirements.</p>
    </div>
    <div class="flex gap-2">
      <a href="verify.php" class="btn-primary">
        <?= icon('check-circle','w-4 h-4') ?>
        Verify students
      </a>
    </div>
  </div>

  <?php
  $stats = [
    ['Pending Students',     $pending_students,  'user',         'amber',   'Awaiting review'],
    ['Approved Students',    $approved_students, 'check-circle', 'emerald', 'Validated graduates'],
    ['Pending Requirements', $pending_reqs,      'folder',       'amber',   'Documents to review'],
    ['Total Graduates',      $total_grads,       'cap',          'crimson', 'Issued Graduate IDs'],
  ];
  $tones = [
    'amber'   => ['bg-amber-50',   'text-amber-700',   'border-amber-100'],
    'emerald' => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-100'],
    'crimson' => ['bg-crimson-50', 'text-crimson-700', 'border-crimson-100'],
  ];
  ?>
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <?php foreach ($stats as [$label, $value, $ic, $tone, $sub]):
      [$bg,$tx,$br] = $tones[$tone];
    ?>
      <div class="stat-card">
        <div class="flex items-start justify-between">
          <div>
            <div class="stat-label"><?= e($label) ?></div>
            <div class="stat-value"><?= $value ?></div>
            <div class="stat-sub"><?= e($sub) ?></div>
          </div>
          <div class="w-9 h-9 rounded-lg <?= $bg ?> <?= $tx ?> border <?= $br ?> flex items-center justify-center"><?= icon($ic,'w-[18px] h-[18px]') ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-8 card">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="font-bold text-ink-900">Recently registered students</h3>
        <p class="text-xs text-ink-500 mt-0.5">Newest applications submitted to the registrar.</p>
      </div>
      <a href="verify.php" class="text-xs font-semibold text-crimson-700 hover:underline">Manage all &rarr;</a>
    </div>
    <div class="-mx-6 overflow-x-auto">
      <table class="table-clean">
        <thead>
          <tr>
            <th class="pl-6">Student ID</th>
            <th>Name</th>
            <th>Course</th>
            <th>Status</th>
            <th class="pr-6">Registered</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent as $r): ?>
            <tr>
              <td class="pl-6 font-mono text-xs text-ink-600" data-label="Student ID"><?= e($r['student_id']) ?></td>
              <td class="font-medium text-ink-900" data-label="Name"><?= e($r['full_name']) ?></td>
              <td class="text-ink-600" data-label="Course"><?= e($r['course']) ?></td>
              <td data-label="Status"><?= status_badge($r['status']) ?></td>
              <td class="pr-6 text-xs text-ink-500" data-label="Registered"><?= fmt_datetime($r['created_at']) ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($recent)): ?>
            <tr><td colspan="5" class="empty-state pl-6">
              <div class="empty-icon"><?= icon('user','w-5 h-5') ?></div>
              <div>No students registered yet.</div>
            </td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
