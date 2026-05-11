<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('student');

$uid = current_user_id();
$student = db_select_one($conn, "SELECT * FROM students WHERE user_id=?", 'i', [$uid]);
$req_total = db_count($conn, 'requirements', 'student_id=?', 'i', [$student['id'] ?? 0]);
$req_approved = db_count($conn, 'requirements', "student_id=? AND status='approved'", 'i', [$student['id'] ?? 0]);
$schedules = db_count($conn, 'schedules', 'user_id=?', 'i', [$uid]);
$paid_total = db_select_one($conn, "SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE user_id=? AND status='paid'", 'i', [$uid]);
$announcements = db_select($conn, "SELECT * FROM announcements WHERE audience IN ('all','students') ORDER BY created_at DESC LIMIT 5");

$page_title = 'Student Dashboard';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Welcome, <?= e($_SESSION['full_name']) ?></h1>
      <p class="subtitle">Track your graduation progress and pending requirements.</p>
      <?php if ($student): ?>
        <div class="mt-3 flex items-center gap-2">
          <?= status_badge($student['status']) ?>
          <span class="text-xs text-ink-500">Application status</span>
        </div>
      <?php endif; ?>
    </div>
    <a href="profile.php" class="btn-secondary">
      <?= icon('user','w-4 h-4') ?>
      View profile
    </a>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="stat-card">
      <div class="flex items-start justify-between">
        <div>
          <div class="stat-label">Requirements Approved</div>
          <div class="stat-value"><?= $req_approved ?> <span class="text-ink-300 text-xl">/ <?= $req_total ?></span></div>
          <div class="stat-sub">Validated documents</div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-crimson-50 text-crimson-700 border border-crimson-100 flex items-center justify-center"><?= icon('folder','w-[18px] h-[18px]') ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="flex items-start justify-between">
        <div>
          <div class="stat-label">Scheduled Activities</div>
          <div class="stat-value"><?= $schedules ?></div>
          <div class="stat-sub">Photobooth &amp; events</div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-crimson-50 text-crimson-700 border border-crimson-100 flex items-center justify-center"><?= icon('calendar','w-[18px] h-[18px]') ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="flex items-start justify-between">
        <div>
          <div class="stat-label">Total Paid</div>
          <div class="stat-value"><?= fmt_money($paid_total['total'] ?? 0) ?></div>
          <div class="stat-sub">Confirmed payments</div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center"><?= icon('cash','w-[18px] h-[18px]') ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="flex items-start justify-between">
        <div>
          <div class="stat-label">Student ID</div>
          <div class="stat-value text-[1.25rem]"><?= e($student['student_id'] ?? 'N/A') ?></div>
          <div class="stat-sub"><?= e($student['course'] ?? '') ?></div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-ink-100 text-ink-600 flex items-center justify-center"><?= icon('cap','w-[18px] h-[18px]') ?></div>
      </div>
    </div>
  </div>

  <div class="mt-8 grid lg:grid-cols-2 gap-4">
    <div class="card">
      <div class="flex items-center justify-between mb-1">
        <h3 class="font-bold text-ink-900">Quick actions</h3>
      </div>
      <p class="text-xs text-ink-500 mb-4">Things to complete to finish your graduation requirements.</p>
      <div class="space-y-2">
        <a href="requirements.php" class="group flex items-center gap-3 px-3 py-3 rounded-lg border border-ink-200 hover:border-crimson-200 hover:bg-crimson-50/40 transition">
          <span class="w-9 h-9 rounded-md bg-crimson-50 text-crimson-700 flex items-center justify-center"><?= icon('folder','w-4 h-4') ?></span>
          <span class="flex-1">
            <span class="block text-sm font-semibold text-ink-800">Upload requirements</span>
            <span class="block text-xs text-ink-500">Submit clearance, photos, and forms</span>
          </span>
          <svg class="w-4 h-4 text-ink-300 group-hover:text-crimson-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="schedules.php" class="group flex items-center gap-3 px-3 py-3 rounded-lg border border-ink-200 hover:border-crimson-200 hover:bg-crimson-50/40 transition">
          <span class="w-9 h-9 rounded-md bg-crimson-50 text-crimson-700 flex items-center justify-center"><?= icon('calendar','w-4 h-4') ?></span>
          <span class="flex-1">
            <span class="block text-sm font-semibold text-ink-800">Book a schedule</span>
            <span class="block text-xs text-ink-500">Photobooth, measurement, rehearsal</span>
          </span>
          <svg class="w-4 h-4 text-ink-300 group-hover:text-crimson-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="payments.php" class="group flex items-center gap-3 px-3 py-3 rounded-lg border border-ink-200 hover:border-crimson-200 hover:bg-crimson-50/40 transition">
          <span class="w-9 h-9 rounded-md bg-crimson-50 text-crimson-700 flex items-center justify-center"><?= icon('cash','w-4 h-4') ?></span>
          <span class="flex-1">
            <span class="block text-sm font-semibold text-ink-800">Submit a payment</span>
            <span class="block text-xs text-ink-500">Yearbook, toga, graduation fees</span>
          </span>
          <svg class="w-4 h-4 text-ink-300 group-hover:text-crimson-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>

    <div class="card">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-ink-900">Latest announcements</h3>
        <a href="announcements.php" class="text-xs font-semibold text-crimson-700 hover:underline">View all &rarr;</a>
      </div>
      <ul class="mt-4 space-y-3">
        <?php foreach ($announcements as $a): ?>
          <li class="rounded-lg border border-ink-200 bg-white px-4 py-3 hover:border-crimson-200 transition">
            <div class="flex items-start gap-3">
              <div class="w-1 self-stretch rounded-full bg-crimson-700"></div>
              <div class="flex-1 min-w-0">
                <div class="font-semibold text-sm text-ink-900"><?= e($a['title']) ?></div>
                <div class="text-xs text-ink-600 mt-1 leading-relaxed"><?= e(mb_substr($a['body'],0,140)) ?>&hellip;</div>
                <div class="text-[11px] text-ink-400 mt-2"><?= fmt_datetime($a['created_at']) ?></div>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
        <?php if (empty($announcements)): ?>
          <li class="empty-state">
            <div class="empty-icon"><?= icon('megaphone','w-5 h-5') ?></div>
            <div>No announcements yet.</div>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
