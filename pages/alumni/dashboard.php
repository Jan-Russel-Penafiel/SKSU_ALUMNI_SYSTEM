<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('alumni');

$uid = current_user_id();
$alumni = db_select_one($conn, "SELECT a.*, g.graduate_id, g.course, g.department, g.graduation_date FROM alumni a JOIN graduates g ON g.id=a.graduate_id WHERE a.user_id=?", 'i', [$uid]);
$events = db_select($conn, "SELECT * FROM events WHERE status='upcoming' ORDER BY event_date LIMIT 5");
$tracer = db_count($conn, 'tracer_reports', 'alumni_id=?', 'i', [$alumni['id'] ?? 0]);

$page_title = 'Alumni Dashboard';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Welcome back, <?= e($_SESSION['full_name']) ?></h1>
      <p class="subtitle">Stay connected with your alma mater, SKSU Isulan.</p>
    </div>
    <a href="tracer.php" class="btn-primary">
      <?= icon('document','w-4 h-4') ?>
      Submit tracer
    </a>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="stat-card">
      <div class="flex items-start justify-between">
        <div>
          <div class="stat-label">Graduate ID</div>
          <div class="stat-value font-mono text-[1.1rem]"><?= e($alumni['graduate_id'] ?? '') ?></div>
          <div class="stat-sub">Permanent record</div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-crimson-50 text-crimson-700 border border-crimson-100 flex items-center justify-center"><?= icon('cap','w-[18px] h-[18px]') ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="flex items-start justify-between">
        <div>
          <div class="stat-label">Course</div>
          <div class="stat-value text-[1rem]"><?= e($alumni['course'] ?? '') ?></div>
          <div class="stat-sub"><?= e($alumni['department'] ?? '') ?></div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-ink-100 text-ink-600 flex items-center justify-center"><?= icon('briefcase','w-[18px] h-[18px]') ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="flex items-start justify-between">
        <div>
          <div class="stat-label">Employment</div>
          <div class="mt-2"><?= status_badge($alumni['employment_status'] ?? 'Unemployed') ?></div>
          <div class="stat-sub mt-2">Current status</div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-100 flex items-center justify-center"><?= icon('check-circle','w-[18px] h-[18px]') ?></div>
      </div>
    </div>
    <div class="stat-card">
      <div class="flex items-start justify-between">
        <div>
          <div class="stat-label">Tracer Submissions</div>
          <div class="stat-value"><?= $tracer ?></div>
          <div class="stat-sub">Quarterly reports</div>
        </div>
        <div class="w-9 h-9 rounded-lg bg-crimson-50 text-crimson-700 border border-crimson-100 flex items-center justify-center"><?= icon('document','w-[18px] h-[18px]') ?></div>
      </div>
    </div>
  </div>

  <div class="mt-8 grid lg:grid-cols-2 gap-4">
    <div class="card">
      <h3 class="font-bold text-ink-900">Quick links</h3>
      <p class="text-xs text-ink-500 mt-0.5 mb-4">Common alumni actions.</p>
      <div class="space-y-2">
        <a href="profile.php" class="group flex items-center gap-3 px-3 py-3 rounded-lg border border-ink-200 hover:border-crimson-200 hover:bg-crimson-50/40 transition">
          <span class="w-9 h-9 rounded-md bg-crimson-50 text-crimson-700 flex items-center justify-center"><?= icon('user','w-4 h-4') ?></span>
          <span class="flex-1">
            <span class="block text-sm font-semibold text-ink-800">Update profile</span>
            <span class="block text-xs text-ink-500">Employment, contact, address</span>
          </span>
          <svg class="w-4 h-4 text-ink-300 group-hover:text-crimson-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="tracer.php" class="group flex items-center gap-3 px-3 py-3 rounded-lg border border-ink-200 hover:border-crimson-200 hover:bg-crimson-50/40 transition">
          <span class="w-9 h-9 rounded-md bg-crimson-50 text-crimson-700 flex items-center justify-center"><?= icon('document','w-4 h-4') ?></span>
          <span class="flex-1">
            <span class="block text-sm font-semibold text-ink-800">Submit tracer survey</span>
            <span class="block text-xs text-ink-500">Quarterly career snapshot</span>
          </span>
          <svg class="w-4 h-4 text-ink-300 group-hover:text-crimson-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
        <a href="events.php" class="group flex items-center gap-3 px-3 py-3 rounded-lg border border-ink-200 hover:border-crimson-200 hover:bg-crimson-50/40 transition">
          <span class="w-9 h-9 rounded-md bg-crimson-50 text-crimson-700 flex items-center justify-center"><?= icon('sparkles','w-4 h-4') ?></span>
          <span class="flex-1">
            <span class="block text-sm font-semibold text-ink-800">Browse events</span>
            <span class="block text-xs text-ink-500">Homecoming &amp; reunions</span>
          </span>
          <svg class="w-4 h-4 text-ink-300 group-hover:text-crimson-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>

    <div class="card">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-ink-900">Upcoming events</h3>
        <a href="events.php" class="text-xs font-semibold text-crimson-700 hover:underline">View all &rarr;</a>
      </div>
      <ul class="mt-4 space-y-3">
        <?php foreach ($events as $ev): ?>
          <li class="rounded-lg border border-ink-200 bg-white px-4 py-3 flex items-start gap-3 hover:border-crimson-200 transition">
            <div class="shrink-0 w-12 text-center rounded-md bg-crimson-50 text-crimson-700 border border-crimson-100 py-1.5">
              <div class="text-[10px] uppercase font-bold tracking-wider"><?= date('M', strtotime($ev['event_date'])) ?></div>
              <div class="text-base font-extrabold leading-none"><?= date('d', strtotime($ev['event_date'])) ?></div>
            </div>
            <div class="min-w-0 flex-1">
              <div class="font-semibold text-sm text-ink-900 truncate"><?= e($ev['title']) ?></div>
              <div class="text-xs text-ink-500 mt-0.5"><?= fmt_date($ev['event_date']) ?> &middot; <?= e($ev['location']) ?></div>
            </div>
          </li>
        <?php endforeach; ?>
        <?php if (empty($events)): ?>
          <li class="empty-state">
            <div class="empty-icon"><?= icon('sparkles','w-5 h-5') ?></div>
            <div>No upcoming events.</div>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
