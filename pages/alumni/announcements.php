<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('alumni');

$all_rows = db_select($conn, "SELECT * FROM announcements WHERE audience IN ('all','alumni') ORDER BY created_at DESC");
$pg = paginate($all_rows, 8);
$rows = $pg['rows'];

$page_title = 'Announcements';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Announcements</h1>
      <p class="subtitle">Updates from the institution and registrar's office.</p>
    </div>
  </div>

  <div class="space-y-3">
    <?php foreach ($rows as $r): ?>
      <div class="card relative">
        <div class="absolute left-0 top-4 bottom-4 w-1 rounded-r-full bg-crimson-700"></div>
        <div class="flex justify-between gap-4 pl-3">
          <h3 class="font-bold text-ink-900"><?= e($r['title']) ?></h3>
          <span class="text-xs text-ink-500 shrink-0"><?= fmt_datetime($r['created_at']) ?></span>
        </div>
        <p class="text-xs text-ink-700 mt-2 pl-3 whitespace-pre-line leading-relaxed"><?= e($r['body']) ?></p>
      </div>
    <?php endforeach; ?>
    <?php if (empty($rows)): ?>
      <div class="card empty-state">
        <div class="empty-icon"><?= icon('megaphone','w-5 h-5') ?></div>
        <div>No announcements yet.</div>
      </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($rows)): ?>
    <div class="mt-4">
      <?php $pg_html = render_pagination($pg); echo str_replace('class="pagination"', 'class="pagination is-standalone"', $pg_html); ?>
    </div>
  <?php endif; ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
