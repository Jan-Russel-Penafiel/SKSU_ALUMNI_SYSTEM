<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('alumni');

$uid = current_user_id();
$events = db_select($conn, "SELECT e.*, (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id=e.id) AS registered,
    EXISTS(SELECT 1 FROM event_registrations er WHERE er.event_id=e.id AND er.user_id=?) AS joined
    FROM events e WHERE e.status IN ('upcoming','ongoing') ORDER BY e.event_date ASC", 'i', [$uid]);

$page_title = 'Alumni Events';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">Alumni Events & Activities</h1>
  <p class="text-sm text-gray-500">Stay engaged — register for upcoming events.</p>

  <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($events as $ev): ?>
      <div class="card flex flex-col">
        <div class="flex items-start justify-between"><h3 class="font-bold text-gray-900"><?= e($ev['title']) ?></h3><?= status_badge($ev['status']) ?></div>
        <p class="text-sm text-gray-600 mt-2 flex-1"><?= e($ev['description']) ?></p>
        <div class="text-xs text-gray-500 mt-3 space-y-1.5">
          <div class="flex items-center gap-2"><?= icon('calendar','w-4 h-4') ?> <?= fmt_date($ev['event_date']) ?> · <?= e($ev['event_time']) ?></div>
          <div class="flex items-center gap-2"><?= icon('pin','w-4 h-4') ?> <?= e($ev['location']) ?></div>
          <div class="flex items-center gap-2"><?= icon('users','w-4 h-4') ?> <?= (int)$ev['registered'] ?> registered<?= $ev['capacity']?' / '.$ev['capacity'].' capacity':'' ?></div>
        </div>
        <?php if ($ev['joined']): ?>
          <button disabled class="mt-4 inline-flex items-center justify-center gap-2 bg-gray-200 text-gray-500 font-semibold py-2 rounded-lg"><?= icon('check','w-4 h-4') ?> Registered</button>
        <?php else: ?>
          <a href="<?= APP_URL ?>/actions/alumni_join_event.php?id=<?= (int)$ev['id'] ?>" class="mt-4 bg-crimson-700 hover:bg-crimson-800 text-white text-center font-semibold py-2 rounded-lg">Register</a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if (empty($events)): ?><div class="card col-span-full text-center text-gray-400">No events available.</div><?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
