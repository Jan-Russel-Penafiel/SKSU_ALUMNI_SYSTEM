<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('admin');

$q = trim($_GET['q'] ?? '');
$status = $_GET['status'] ?? '';
$batch  = $_GET['batch'] ?? '';

$conds = ['1']; $types = ''; $params = [];
if ($q) { $conds[] = '(u.full_name LIKE ? OR g.graduate_id LIKE ?)'; $types .= 'ss'; $params[] = "%$q%"; $params[] = "%$q%"; }
if ($status) { $conds[] = 'a.employment_status=?'; $types .= 's'; $params[] = $status; }
if ($batch) { $conds[] = 'g.academic_year=?'; $types .= 's'; $params[] = $batch; }
$where = implode(' AND ', $conds);

$all_rows = db_select($conn, "SELECT a.*, u.full_name, u.email, g.graduate_id, g.course, g.academic_year
    FROM alumni a JOIN users u ON u.id=a.user_id JOIN graduates g ON g.id=a.graduate_id WHERE $where ORDER BY a.id ASC", $types, $params);
$pg = paginate($all_rows, 10);
$rows = $pg['rows'];

$batches = db_select($conn, "SELECT DISTINCT academic_year FROM graduates WHERE academic_year IS NOT NULL ORDER BY academic_year DESC");

$page_title = 'Alumni List';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Alumni Records</h1>
      <p class="subtitle">Search and filter the full alumni directory.</p>
    </div>
  </div>

  <form class="card grid sm:grid-cols-4 gap-3 mb-6 items-end">
    <div><label class="label">Search</label><input name="q" value="<?= e($q) ?>" class="input" placeholder="Name or Graduate ID"></div>
    <div><label class="label">Employment</label>
      <select name="status" class="input"><option value="">All</option>
        <?php foreach (['Employed','Unemployed','Self-Employed','Further Studies'] as $s): ?>
          <option <?= $status===$s?'selected':'' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label class="label">Batch (AY)</label>
      <select name="batch" class="input"><option value="">All</option>
        <?php foreach ($batches as $b): ?><option <?= $batch===$b['academic_year']?'selected':'' ?>><?= e($b['academic_year']) ?></option><?php endforeach; ?>
      </select>
    </div>
    <div class="flex gap-2">
      <button class="btn-primary">Filter</button>
      <a href="?" class="btn-ghost">Reset</a>
    </div>
  </form>

  <div class="table-wrap has-pagination overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Graduate ID</th>
          <th>Name</th>
          <th>Course</th>
          <th>AY</th>
          <th>Status</th>
          <th>Company</th>
          <th>Job</th>
          <th class="pr-6">Last Updated</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="pl-6 font-mono text-xs font-bold text-crimson-700" data-label="Graduate ID"><?= e($r['graduate_id']) ?></td>
            <td data-label="Name">
              <div class="font-semibold text-ink-900"><?= e($r['full_name']) ?></div>
              <div class="text-xs text-ink-500"><?= e($r['email']) ?></div>
            </td>
            <td class="text-ink-700" data-label="Course"><?= e($r['course']) ?></td>
            <td class="text-ink-600" data-label="AY"><?= e($r['academic_year']) ?></td>
            <td data-label="Status"><?= status_badge($r['employment_status']) ?></td>
            <td class="text-ink-600" data-label="Company"><?= e($r['company_name'] ?: '') ?></td>
            <td class="text-ink-600" data-label="Job"><?= e($r['job_title'] ?: '') ?></td>
            <td class="pr-6 text-xs text-ink-500" data-label="Last Updated"><?= fmt_datetime($r['last_updated']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="8"><div class="empty-state">
            <div class="empty-icon"><?= icon('briefcase','w-5 h-5') ?></div>
            <div>No alumni records found.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
