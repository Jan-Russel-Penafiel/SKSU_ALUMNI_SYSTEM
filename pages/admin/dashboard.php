<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('admin');

$total_users   = db_count($conn, 'users');
$total_students= db_count($conn, 'students');
$total_grads   = db_count($conn, 'graduates');
$total_alumni  = db_count($conn, 'alumni');
$total_events  = db_count($conn, 'events');
$paid_total    = db_select_one($conn, "SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE status='paid'");
$emp_employed  = db_count($conn, 'alumni', "employment_status='Employed'");
$emp_unemp     = db_count($conn, 'alumni', "employment_status='Unemployed'");
$emp_self      = db_count($conn, 'alumni', "employment_status='Self-Employed'");
$emp_studies   = db_count($conn, 'alumni', "employment_status='Further Studies'");

$page_title = 'Admin Dashboard';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Administrator Dashboard</h1>
      <p class="subtitle">System overview of users, graduates, alumni and engagement at a glance.</p>
    </div>
  
  </div>

  <?php
  $stats = [
    ['Total Users',       $total_users,                 'users',       'All registered accounts'],
    ['Students',          $total_students,              'cap',         'Active graduating students'],
    ['Graduates',         $total_grads,                 'check-circle','Validated graduates'],
    ['Alumni',            $total_alumni,                'briefcase',   'Tracked alumni records'],
    ['Total Revenue',     fmt_money($paid_total['s']),  'cash',        'Confirmed payments'],
    ['Events',            $total_events,                'sparkles',    'Posted alumni events'],
    ['Employed Alumni',   $emp_employed,                'briefcase',   'Currently employed'],
    ['Unemployed Alumni', $emp_unemp,                   'user',        'Awaiting placement'],
  ];
  ?>
  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <?php foreach ($stats as [$label, $value, $ic, $sub]): ?>
      <div class="stat-card">
        <div class="flex items-start justify-between">
          <div>
            <div class="stat-label"><?= e($label) ?></div>
            <div class="stat-value"><?= $value ?></div>
            <div class="stat-sub"><?= e($sub) ?></div>
          </div>
          <div class="w-9 h-9 rounded-lg bg-crimson-50 text-crimson-700 border border-crimson-100 flex items-center justify-center shrink-0">
            <?= icon($ic, 'w-[18px] h-[18px]') ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-8 grid lg:grid-cols-3 gap-4">
    <div class="card lg:col-span-2">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="font-bold text-ink-900">Employment Distribution</h3>
          <p class="text-xs text-ink-500 mt-0.5">Breakdown of alumni by current employment status.</p>
        </div>
        <span class="badge badge-neutral"><?= ($emp_employed + $emp_unemp + $emp_self + $emp_studies) ?> tracked</span>
      </div>
      <?php $sum = max(1, $emp_employed + $emp_unemp + $emp_self + $emp_studies); ?>
      <div class="space-y-4">
        <?php foreach ([
          ['Employed',        $emp_employed, 'bg-emerald-500', 'text-emerald-700'],
          ['Self-Employed',   $emp_self,     'bg-indigo-500',  'text-indigo-700'],
          ['Further Studies', $emp_studies,  'bg-violet-500',  'text-violet-700'],
          ['Unemployed',      $emp_unemp,    'bg-rose-500',    'text-rose-700'],
        ] as [$l,$c,$bar,$tx]):
          $pct = round($c/$sum*100, 1); ?>
          <div>
            <div class="flex justify-between items-center text-sm">
              <span class="font-semibold text-ink-800"><?= $l ?></span>
              <span class="text-ink-500"><span class="font-semibold <?= $tx ?>"><?= $c ?></span> &middot; <?= $pct ?>%</span>
            </div>
            <div class="bg-ink-100 rounded-full h-2.5 mt-2 overflow-hidden">
              <div class="<?= $bar ?> h-full rounded-full transition-all duration-500" style="width: <?= $pct ?>%"></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="card">
      <h3 class="font-bold text-ink-900">Quick actions</h3>
      <p class="text-xs text-ink-500 mt-0.5 mb-4">Common administrative shortcuts.</p>
      <div class="space-y-2">
        <?php
        $actions = [
          ['Manage users',         'users',         'pages/admin/users.php'],
          ['Review students',      'cap',           'pages/admin/students.php'],
          ['Alumni directory',     'briefcase',     'pages/admin/alumni_list.php'],
          ['Post announcement',    'megaphone',     'pages/admin/announcements.php'],
        ];
        foreach ($actions as [$lbl,$ic,$href]): ?>
          <a href="<?= APP_URL ?>/<?= $href ?>" class="flex items-center gap-3 p-2.5 rounded-lg border border-transparent hover:border-ink-200 hover:bg-ink-50 transition">
            <span class="w-8 h-8 rounded-md bg-ink-100 text-ink-600 flex items-center justify-center"><?= icon($ic,'w-4 h-4') ?></span>
            <span class="text-sm font-medium text-ink-700 flex-1"><?= e($lbl) ?></span>
            <svg class="w-4 h-4 text-ink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
