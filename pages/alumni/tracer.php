<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('alumni');

$uid = current_user_id();
$alumni = db_select_one($conn, "SELECT * FROM alumni WHERE user_id=?", 'i', [$uid]);
$reports = $alumni ? db_select($conn, "SELECT * FROM tracer_reports WHERE alumni_id=? ORDER BY report_year DESC, quarter DESC", 'i', [$alumni['id']]) : [];

$page_title = 'Tracer Survey';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Tracer Monitoring</h1>
      <p class="subtitle">Submit quarterly tracer surveys for alumni tracking.</p>
    </div>
    <button type="button" class="btn-primary" data-modal-open="tracerModal">
      <?= icon('document','w-4 h-4') ?>
      Submit report
    </button>
  </div>

  <div class="table-wrap overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Year</th>
          <th>Quarter</th>
          <th>Status</th>
          <th>Company</th>
          <th>Job</th>
          <th>Course-related</th>
          <th class="pr-6">Submitted</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reports as $r): ?>
          <tr>
            <td class="pl-6 font-medium text-ink-800"><?= e($r['report_year']) ?></td>
            <td class="text-ink-700"><?= e($r['quarter']) ?></td>
            <td><?= status_badge($r['employment_status']) ?></td>
            <td class="text-ink-600"><?= e($r['company_name'] ?: '&mdash;') ?></td>
            <td class="text-ink-600"><?= e($r['job_title'] ?: '&mdash;') ?></td>
            <td class="text-ink-600"><?= e($r['related_to_course']) ?></td>
            <td class="pr-6 text-xs text-ink-500"><?= fmt_datetime($r['submitted_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($reports)): ?>
          <tr><td colspan="7"><div class="empty-state">
            <div class="empty-icon"><?= icon('document','w-5 h-5') ?></div>
            <div>No reports submitted yet.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<!-- Submit Tracer Modal -->
<div id="tracerModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="tracerModalTitle">
  <div class="modal-panel modal-lg" role="document">
    <div class="modal-head">
      <div>
        <h3 id="tracerModalTitle">Submit quarterly tracer report</h3>
        <div class="modal-sub">Helps the institution monitor alumni outcomes and graduate tracer KPIs.</div>
      </div>
      <button type="button" class="modal-close" data-modal-close aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form action="<?= APP_URL ?>/actions/alumni_submit_tracer.php" method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="modal-body grid sm:grid-cols-2 gap-4">
        <div><label class="label">Year</label><input type="number" name="report_year" class="input" min="2020" max="2099" value="<?= date('Y') ?>" required></div>
        <div><label class="label">Quarter</label>
          <select name="quarter" class="input">
            <option>Q1</option><option>Q2</option><option>Q3</option><option>Q4</option>
          </select>
        </div>
        <div class="sm:col-span-2"><label class="label">Employment status</label>
          <select name="employment_status" class="input">
            <option>Employed</option>
            <option>Unemployed</option>
            <option>Self-Employed</option>
            <option>Further Studies</option>
          </select>
        </div>
        <div><label class="label">Company / organization</label><input type="text" name="company_name" class="input"></div>
        <div><label class="label">Job title</label><input type="text" name="job_title" class="input"></div>
        <div class="sm:col-span-2"><label class="label">Related to course?</label>
          <select name="related_to_course" class="input">
            <option>Yes</option><option>No</option><option>Partially</option>
          </select>
        </div>
        <div class="sm:col-span-2"><label class="label">Notes</label><textarea name="notes" class="input" rows="3"></textarea></div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-secondary" data-modal-close>Cancel</button>
        <button type="submit" class="btn-primary">
          <?= icon('document','w-4 h-4') ?>
          Submit report
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
