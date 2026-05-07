<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('admin');

$rows = db_select($conn, "SELECT a.*, u.full_name FROM announcements a LEFT JOIN users u ON u.id=a.posted_by ORDER BY a.created_at DESC");

$page_title = 'Announcements';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Announcements</h1>
      <p class="subtitle">Broadcast updates to students, alumni, or staff.</p>
    </div>
    <button type="button" class="btn-primary" data-modal-open="announcementModal">
      <?= icon('plus','w-4 h-4') ?>
      New announcement
    </button>
  </div>

  <div class="space-y-3">
    <?php foreach ($rows as $r): ?>
      <div class="card relative">
        <div class="absolute left-0 top-4 bottom-4 w-1 rounded-r-full bg-crimson-700"></div>
        <div class="flex justify-between items-start gap-4 pl-3">
          <div class="min-w-0 flex-1">
            <h3 class="font-bold text-ink-900"><?= e($r['title']) ?></h3>
            <div class="text-xs text-ink-500 mt-0.5 flex flex-wrap gap-x-2 gap-y-1 items-center">
              <span><?= fmt_datetime($r['created_at']) ?></span>
              <span class="w-1 h-1 rounded-full bg-ink-300"></span>
              <span><?= e($r['full_name']) ?></span>
              <span class="w-1 h-1 rounded-full bg-ink-300"></span>
              <span class="badge badge-info capitalize"><?= e($r['audience']) ?></span>
            </div>
          </div>
          <a href="<?= APP_URL ?>/actions/admin_delete_announcement.php?id=<?= (int)$r['id'] ?>"
             data-confirm="Delete announcement?"
             class="text-xs font-semibold text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-md shrink-0">Delete</a>
        </div>
        <p class="text-sm text-ink-700 mt-3 pl-3 whitespace-pre-line leading-relaxed"><?= e($r['body']) ?></p>
      </div>
    <?php endforeach; ?>
    <?php if (empty($rows)): ?>
      <div class="card empty-state">
        <div class="empty-icon"><?= icon('megaphone','w-5 h-5') ?></div>
        <div>No announcements yet.</div>
      </div>
    <?php endif; ?>
  </div>
</main>

<!-- New Announcement Modal -->
<div id="announcementModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="announcementModalTitle">
  <div class="modal-panel" role="document">
    <div class="modal-head">
      <div>
        <h3 id="announcementModalTitle">Post new announcement</h3>
        <div class="modal-sub">Reaches the selected audience instantly.</div>
      </div>
      <button type="button" class="modal-close" data-modal-close aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form action="<?= APP_URL ?>/actions/admin_save_announcement.php" method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="modal-body grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2"><label class="label">Title</label><input type="text" name="title" class="input" required></div>
        <div class="sm:col-span-2"><label class="label">Body</label><textarea name="body" class="input" rows="5" required placeholder="Write the announcement message..."></textarea></div>
        <div class="sm:col-span-2"><label class="label">Audience</label>
          <select name="audience" class="input">
            <option value="all">All users</option>
            <option value="students">Students only</option>
            <option value="alumni">Alumni only</option>
            <option value="registrar">Registrar staff</option>
          </select>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-secondary" data-modal-close>Cancel</button>
        <button type="submit" class="btn-primary">
          <?= icon('megaphone','w-4 h-4') ?>
          Post announcement
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
