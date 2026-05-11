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
        ['Payments',          'pages/registrar/payments.php',      'cash'],
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
        ['Events',            'pages/admin/events.php',         'sparkles'],
        ['Announcements',     'pages/admin/announcements.php',  'megaphone'],
        ['Reports',           'pages/admin/reports.php',        'chart'],
    ];
}
?>
<div class="hidden md:block w-64 shrink-0" aria-hidden="true"></div>
<aside id="sidebar" class="hidden md:block w-64 fixed top-16 left-0 bottom-0 z-20" style="left: max(0px, calc((100vw - 1400px) / 2));">
  <div class="bg-white border-r border-ink-200 h-full flex flex-col overflow-hidden">
    <nav class="p-3 flex-1 overflow-hidden min-h-0">
      <div class="px-3 pt-1 pb-1 text-[10px] uppercase font-bold text-ink-400 tracking-[0.12em]">Navigation</div>
      <div class="space-y-0.5">
        <?php foreach ($nav as [$label, $path, $iconName]):
            $active = strpos($_SERVER['PHP_SELF'], basename($path)) !== false; ?>
          <a href="<?= APP_URL . '/' . $path ?>"
             class="group relative flex items-center gap-3 px-3 py-2 rounded-lg text-[12.5px] font-medium transition <?= $active ? 'bg-crimson-50 text-crimson-800' : 'text-ink-600 hover:bg-ink-50 hover:text-ink-900' ?>">
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

    <div class="px-4 pb-3 pt-2 border-t border-ink-100 shrink-0">
      <div class="text-[11px] text-ink-400 text-center">v1.0 &middot; SKSU Isulan</div>
    </div>
  </div>
</aside>
