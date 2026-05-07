<?php
// =============================================================
// Sidebar - role-based navigation (sticky, no internal scroll)
// =============================================================
$role = current_role();

$nav = [];
if ($role === 'student') {
    $nav = [
        ['Dashboard',         'pages/student/dashboard.php',    'home'],
        ['My Profile',        'pages/student/profile.php',      'user'],
        ['Requirements',      'pages/student/requirements.php', 'folder'],
        ['Schedules',         'pages/student/schedules.php',    'calendar'],
        ['Payments',          'pages/student/payments.php',     'cash'],
        ['Announcements',     'pages/student/announcements.php','megaphone'],
    ];
} elseif ($role === 'registrar') {
    $nav = [
        ['Dashboard',         'pages/registrar/dashboard.php',     'home'],
        ['Verify Students',   'pages/registrar/verify.php',        'check-circle'],
        ['Requirements',      'pages/registrar/requirements.php',  'folder'],
        ['Graduates',         'pages/registrar/graduates.php',     'cap'],
        ['Masterlist',        'pages/registrar/masterlist.php',    'list'],
    ];
} elseif ($role === 'alumni') {
    $nav = [
        ['Dashboard',         'pages/alumni/dashboard.php',     'home'],
        ['My Profile',        'pages/alumni/profile.php',       'user'],
        ['Tracer Survey',     'pages/alumni/tracer.php',        'document'],
        ['Events',            'pages/alumni/events.php',        'sparkles'],
        ['Announcements',     'pages/alumni/announcements.php', 'megaphone'],
    ];
} elseif ($role === 'admin') {
    $nav = [
        ['Dashboard',         'pages/admin/dashboard.php',      'home'],
        ['Users',             'pages/admin/users.php',          'users'],
        ['Students',          'pages/admin/students.php',       'cap'],
        ['Alumni',            'pages/admin/alumni_list.php',    'briefcase'],
        ['Payments',          'pages/admin/payments.php',       'cash'],
        ['Events',            'pages/admin/events.php',         'sparkles'],
        ['Announcements',     'pages/admin/announcements.php',  'megaphone'],
        ['Reports',           'pages/admin/reports.php',        'chart'],
    ];
}
?>
<aside id="sidebar" class="hidden md:block w-64 shrink-0 self-start sticky top-16 z-20">
  <div class="bg-white border-r border-ink-200 h-[calc(100vh-4rem)] flex flex-col overflow-hidden">
    <nav class="p-3 flex-1 overflow-hidden">
      <div class="px-3 pt-2 pb-1.5 text-[10px] uppercase font-bold text-ink-400 tracking-[0.12em]">Navigation</div>
      <div class="space-y-0.5">
        <?php foreach ($nav as [$label, $path, $iconName]):
            $active = strpos($_SERVER['PHP_SELF'], basename($path)) !== false; ?>
          <a href="<?= APP_URL . '/' . $path ?>"
             class="group relative flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13.5px] font-medium transition <?= $active ? 'bg-crimson-50 text-crimson-800' : 'text-ink-600 hover:bg-ink-50 hover:text-ink-900' ?>">
            <?php if ($active): ?>
              <span class="absolute left-0 top-1.5 bottom-1.5 w-1 rounded-r-full bg-crimson-700"></span>
            <?php endif; ?>
            <span class="<?= $active ? 'text-crimson-700' : 'text-ink-400 group-hover:text-ink-600' ?>">
              <?= icon($iconName, 'w-[18px] h-[18px] shrink-0') ?>
            </span>
            <span><?= e($label) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </nav>

    <div class="px-4 pb-4 pt-3 border-t border-ink-100 shrink-0">
      <div class="rounded-lg bg-gradient-to-br from-crimson-700 to-crimson-900 text-white p-3">
        <div class="text-[10px] uppercase tracking-[0.1em] text-crimson-100 font-semibold">Need help?</div>
        <div class="mt-0.5 text-xs font-semibold leading-snug">Contact the Registrar's Office for account or document concerns.</div>
      </div>
      <div class="text-[11px] text-ink-400 mt-3 text-center">v1.0 &middot; SKSU Isulan</div>
    </div>
  </div>
</aside>
