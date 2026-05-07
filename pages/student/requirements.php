<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('student');

$uid = current_user_id();
$student = db_select_one($conn, "SELECT * FROM students WHERE user_id=?", 'i', [$uid]);
$all_reqs = $student ? db_select($conn, "SELECT * FROM requirements WHERE student_id=? ORDER BY id ASC", 'i', [$student['id']]) : [];
$pg = paginate($all_reqs, 10);
$reqs = $pg['rows'];

$page_title = 'Requirements';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Graduation Requirements</h1>
      <p class="subtitle">Upload and monitor your submitted documents.</p>
    </div>
    <button type="button" class="btn-primary" data-modal-open="reqModal">
      <?= icon('upload','w-4 h-4') ?>
      Upload requirement
    </button>
  </div>

  <div class="table-wrap has-pagination overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Type</th>
          <th>Title</th>
          <th>Status</th>
          <th>Remarks</th>
          <th>Uploaded</th>
          <th class="pr-6 text-right">File</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reqs as $r): ?>
          <tr>
            <td class="pl-6 font-medium text-ink-800" data-label="Type"><?= e($r['requirement_type']) ?></td>
            <td class="text-ink-700" data-label="Title"><?= e($r['title']) ?></td>
            <td data-label="Status"><?= status_badge($r['status']) ?></td>
            <td class="text-xs text-ink-500" data-label="Remarks"><?= e($r['remarks'] ?: '') ?></td>
            <td class="text-xs text-ink-500" data-label="Uploaded"><?= fmt_datetime($r['uploaded_at']) ?></td>
            <td class="pr-6 text-right" data-label="File">
              <a href="<?= APP_URL ?>/assets/<?= e($r['file_path']) ?>" target="_blank" class="text-xs font-semibold text-crimson-700 hover:bg-crimson-50 px-2 py-1 rounded-md">View</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($reqs)): ?>
          <tr><td colspan="6"><div class="empty-state">
            <div class="empty-icon"><?= icon('folder','w-5 h-5') ?></div>
            <div>No requirements submitted yet.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>

<!-- Upload Requirement Modal -->
<div id="reqModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="reqModalTitle">
  <div class="modal-panel" role="document">
    <div class="modal-head">
      <div>
        <h3 id="reqModalTitle">Upload requirement</h3>
        <div class="modal-sub">Allowed: PDF, JPG, PNG, DOC, DOCX (max 10 MB).</div>
      </div>
      <button type="button" class="modal-close" data-modal-close aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form action="<?= APP_URL ?>/actions/student_upload_requirement.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="modal-body grid sm:grid-cols-2 gap-4">
        <div><label class="label">Type</label>
          <select name="requirement_type" class="input" required>
            <option>Clearance</option>
            <option>Yearbook Form</option>
            <option>Graduation Document</option>
            <option>Other</option>
          </select>
        </div>
        <div><label class="label">Title / Description</label><input type="text" name="title" class="input" required></div>
        <div class="sm:col-span-2"><label class="label">File</label><input type="file" name="file" class="input" required></div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-secondary" data-modal-close>Cancel</button>
        <button type="submit" class="btn-primary">
          <?= icon('upload','w-4 h-4') ?>
          Upload
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
