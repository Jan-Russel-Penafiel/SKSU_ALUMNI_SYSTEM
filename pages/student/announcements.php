<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('student');

$rows = db_select($conn, "SELECT * FROM announcements WHERE audience IN ('all','students') ORDER BY created_at DESC");

$page_title = 'Announcements';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">Announcements</h1>
  <div class="mt-6 space-y-4">
    <?php foreach ($rows as $r): ?>
      <div class="card border-l-4 border-crimson-700">
        <div class="flex justify-between"><h3 class="font-bold text-gray-900"><?= e($r['title']) ?></h3><span class="text-xs text-gray-500"><?= fmt_datetime($r['created_at']) ?></span></div>
        <p class="text-sm text-gray-600 mt-2 whitespace-pre-line"><?= e($r['body']) ?></p>
      </div>
    <?php endforeach; ?>
    <?php if (empty($rows)): ?><div class="card text-center text-gray-400">No announcements.</div><?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
