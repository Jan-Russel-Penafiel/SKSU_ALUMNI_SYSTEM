<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('alumni');

$uid = current_user_id();
$all_events = db_select($conn, "SELECT e.*, (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id=e.id) AS registered,
    EXISTS(SELECT 1 FROM event_registrations er WHERE er.event_id=e.id AND er.user_id=?) AS joined
    FROM events e WHERE e.status IN ('upcoming','ongoing') ORDER BY e.event_date ASC", 'i', [$uid]);
$pg = paginate($all_events, 9);
$events = $pg['rows'];

$page_title = 'Alumni Events';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Alumni Events &amp; Activities</h1>
      <p class="subtitle">Stay engaged &mdash; register for upcoming events.</p>
    </div>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($events as $ev): ?>
      <div class="card flex flex-col">
        <div class="flex items-start justify-between gap-2">
          <h3 class="font-bold text-ink-900 leading-tight"><?= e($ev['title']) ?></h3>
          <?= status_badge($ev['status']) ?>
        </div>
        <p class="text-xs text-ink-600 mt-2 flex-1 leading-relaxed"><?= e($ev['description']) ?></p>
        <div class="text-xs text-ink-500 mt-3 space-y-1.5">
          <div class="flex items-center gap-2"><?= icon('calendar','w-4 h-4 text-ink-400') ?> <?= fmt_date($ev['event_date']) ?> &middot; <?= e($ev['event_time']) ?></div>
          <div class="flex items-center gap-2"><?= icon('pin','w-4 h-4 text-ink-400') ?> <?= e($ev['location']) ?></div>
          <div class="flex items-center gap-2"><?= icon('users','w-4 h-4 text-ink-400') ?> <?= (int)$ev['registered'] ?> registered<?= $ev['capacity']?' / '.$ev['capacity'].' capacity':'' ?></div>
        </div>
        <?php if ($ev['joined']): ?>
          <button disabled class="mt-4 inline-flex items-center justify-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 font-semibold text-xs py-2 rounded-md"><?= icon('check','w-4 h-4') ?> Registered</button>
        <?php else: ?>
          <a href="<?= APP_URL ?>/actions/alumni_join_event.php?id=<?= (int)$ev['id'] ?>" class="btn-primary mt-4 w-full">Register</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if (empty($events)): ?>
      <div class="card col-span-full empty-state">
        <div class="empty-icon"><?= icon('sparkles','w-5 h-5') ?></div>
        <div>No events available.</div>
      </div>
    <?php endif; ?>
  </div>

  <?php if (!empty($events)): ?>
    <div class="mt-4">
      <?php $pg_html = render_pagination($pg); echo str_replace('class="pagination"', 'class="pagination is-standalone"', $pg_html); ?>
    </div>
  <?php endif; ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
