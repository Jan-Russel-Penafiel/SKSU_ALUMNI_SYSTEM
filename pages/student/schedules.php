<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('student');

$uid = current_user_id();
$schedules = db_select($conn, "SELECT * FROM schedules WHERE user_id=? ORDER BY scheduled_date DESC, scheduled_time DESC", 'i', [$uid]);

$page_title = 'My Schedules';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Schedules &amp; Appointments</h1>
      <p class="subtitle">Book photobooth, graduation, and alumni event slots. Conflicts are automatically prevented.</p>
    </div>
    <button type="button" class="btn-primary" data-modal-open="scheduleModal">
      <?= icon('calendar','w-4 h-4') ?>
      Book a slot
    </button>
  </div>

  <div class="table-wrap overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Type</th>
          <th>Title</th>
          <th>Date</th>
          <th>Time</th>
          <th>Location</th>
          <th class="pr-6">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($schedules as $s): ?>
          <tr>
            <td class="pl-6 font-medium text-ink-800"><?= e($s['schedule_type']) ?></td>
            <td class="text-ink-700"><?= e($s['title']) ?></td>
            <td class="text-ink-700"><?= fmt_date($s['scheduled_date']) ?></td>
            <td class="text-ink-600"><?= e($s['scheduled_time']) ?></td>
            <td class="text-ink-600"><?= e($s['location']) ?></td>
            <td class="pr-6"><?= status_badge($s['status']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($schedules)): ?>
          <tr><td colspan="6"><div class="empty-state">
            <div class="empty-icon"><?= icon('calendar','w-5 h-5') ?></div>
            <div>No schedules yet. Book your first slot to get started.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<!-- Book Schedule Modal -->
<div id="scheduleModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="scheduleModalTitle">
  <div class="modal-panel" role="document">
    <div class="modal-head">
      <div>
        <h3 id="scheduleModalTitle">Book a schedule</h3>
        <div class="modal-sub">Reserve a photobooth, graduation, or alumni event slot.</div>
      </div>
      <button type="button" class="modal-close" data-modal-close aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form action="<?= APP_URL ?>/actions/student_book_schedule.php" method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="modal-body grid sm:grid-cols-2 gap-4">
        <div class="sm:col-span-2"><label class="label">Type</label>
          <select name="schedule_type" class="input" required>
            <option>Photobooth</option>
            <option>Graduation</option>
            <option>Alumni Event</option>
          </select>
        </div>
        <div class="sm:col-span-2"><label class="label">Title</label><input type="text" name="title" class="input" required></div>
        <div class="sm:col-span-2"><label class="label">Description</label><textarea name="description" class="input" rows="2"></textarea></div>
        <div><label class="label">Date</label><input type="date" name="scheduled_date" class="input" required></div>
        <div><label class="label">Time</label><input type="time" name="scheduled_time" class="input" required></div>
        <div class="sm:col-span-2"><label class="label">Location</label><input type="text" name="location" class="input"></div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-secondary" data-modal-close>Cancel</button>
        <button type="submit" class="btn-primary">
          <?= icon('calendar','w-4 h-4') ?>
          Book schedule
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
