<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('admin');

$rows = db_select($conn, "SELECT e.*, (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id=e.id) AS registered FROM events e ORDER BY event_date DESC");

$page_title = 'Events';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Manage Events</h1>
      <p class="subtitle">Schedule and oversee alumni events and registrations.</p>
    </div>
    <button type="button" class="btn-primary" data-modal-open="eventModal">
      <?= icon('plus','w-4 h-4') ?>
      New event
    </button>
  </div>

  <div class="table-wrap overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Title</th>
          <th>Date</th>
          <th>Time</th>
          <th>Location</th>
          <th>Registered</th>
          <th>Capacity</th>
          <th>Status</th>
          <th class="pr-6 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $ev): ?>
          <tr>
            <td class="pl-6 font-semibold text-ink-900"><?= e($ev['title']) ?></td>
            <td class="text-ink-700"><?= fmt_date($ev['event_date']) ?></td>
            <td class="text-ink-600"><?= e($ev['event_time']) ?></td>
            <td class="text-ink-600"><?= e($ev['location']) ?></td>
            <td class="text-ink-700 font-medium"><?= (int)$ev['registered'] ?></td>
            <td class="text-ink-600"><?= (int)$ev['capacity'] ?: '&mdash;' ?></td>
            <td><?= status_badge($ev['status']) ?></td>
            <td class="pr-6 text-right">
              <a href="<?= APP_URL ?>/actions/admin_delete_event.php?id=<?= (int)$ev['id'] ?>"
                 data-confirm="Delete this event?"
                 class="text-xs font-semibold text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-md">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="8"><div class="empty-state">
            <div class="empty-icon"><?= icon('sparkles','w-5 h-5') ?></div>
            <div>No events yet. Create your first event to get started.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<!-- New Event Modal -->
<div id="eventModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="eventModalTitle">
  <div class="modal-panel modal-lg" role="document">
    <div class="modal-head">
      <div>
        <h3 id="eventModalTitle">Create new event</h3>
        <div class="modal-sub">Alumni can register once the event is published.</div>
      </div>
      <button type="button" class="modal-close" data-modal-close aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form action="<?= APP_URL ?>/actions/admin_save_event.php" method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="modal-body grid sm:grid-cols-3 gap-4">
        <div class="sm:col-span-3"><label class="label">Title</label><input type="text" name="title" class="input" required placeholder="e.g. Alumni Homecoming 2026"></div>
        <div class="sm:col-span-3"><label class="label">Description</label><textarea name="description" class="input" rows="3" placeholder="Optional details, agenda, dress code..."></textarea></div>
        <div><label class="label">Date</label><input type="date" name="event_date" class="input" required></div>
        <div><label class="label">Time</label><input type="time" name="event_time" class="input" required></div>
        <div><label class="label">Capacity</label><input type="number" name="capacity" class="input" value="0" min="0" placeholder="0 = unlimited"></div>
        <div class="sm:col-span-3"><label class="label">Location</label><input type="text" name="location" class="input" placeholder="e.g. SKSU Isulan Gymnasium"></div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-secondary" data-modal-close>Cancel</button>
        <button type="submit" class="btn-primary">
          <?= icon('sparkles','w-4 h-4') ?>
          Create event
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
