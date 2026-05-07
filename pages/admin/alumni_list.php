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

$rows = db_select($conn, "SELECT a.*, u.full_name, u.email, g.graduate_id, g.course, g.academic_year
    FROM alumni a JOIN users u ON u.id=a.user_id JOIN graduates g ON g.id=a.graduate_id WHERE $where ORDER BY a.last_updated DESC", $types, $params);

$batches = db_select($conn, "SELECT DISTINCT academic_year FROM graduates WHERE academic_year IS NOT NULL ORDER BY academic_year DESC");

$page_title = 'Alumni List';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">Alumni Records</h1>
  <form class="mt-4 grid sm:grid-cols-4 gap-3">
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
    <div class="flex items-end"><button class="btn-primary w-full">Filter</button></div>
  </form>
  <div class="mt-6 card overflow-x-auto">
    <table class="table-clean">
      <thead><tr><th>Graduate ID</th><th>Name</th><th>Course</th><th>AY</th><th>Status</th><th>Company</th><th>Job</th><th>Last Updated</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td class="font-mono text-xs font-bold text-crimson-700"><?= e($r['graduate_id']) ?></td>
            <td>
              <div class="font-semibold"><?= e($r['full_name']) ?></div>
              <div class="text-xs text-gray-500"><?= e($r['email']) ?></div>
            </td>
            <td><?= e($r['course']) ?></td>
            <td><?= e($r['academic_year']) ?></td>
            <td><?= status_badge($r['employment_status']) ?></td>
            <td><?= e($r['company_name'] ?: '—') ?></td>
            <td><?= e($r['job_title'] ?: '—') ?></td>
            <td class="text-xs"><?= fmt_datetime($r['last_updated']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="8" class="text-center text-gray-400 py-6">No alumni found.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
