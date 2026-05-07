<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('admin');

// =============================================================
// Reports & Analytics — Employment, Population, Payments
// =============================================================
$total_grads  = db_count($conn,'graduates');
$total_alumni = db_count($conn,'alumni');
$emp_employed = db_count($conn,'alumni',"employment_status='Employed'");
$emp_unemp    = db_count($conn,'alumni',"employment_status='Unemployed'");
$emp_self     = db_count($conn,'alumni',"employment_status='Self-Employed'");
$emp_studies  = db_count($conn,'alumni',"employment_status='Further Studies'");
$emp_rate     = $total_alumni ? round(($emp_employed + $emp_self) / $total_alumni * 100, 1) : 0;

// By course
$by_course = db_select($conn, "SELECT g.course,
    COUNT(*) AS total,
    SUM(CASE WHEN a.employment_status IN ('Employed','Self-Employed') THEN 1 ELSE 0 END) AS employed
    FROM alumni a JOIN graduates g ON g.id=a.graduate_id GROUP BY g.course ORDER BY total DESC");

// By academic year
$by_year = db_select($conn, "SELECT g.academic_year, COUNT(*) AS total
    FROM graduates g WHERE g.academic_year IS NOT NULL GROUP BY g.academic_year ORDER BY g.academic_year DESC");

// Payment summary
$pay_by_type = db_select($conn, "SELECT payment_type, SUM(amount) AS total, COUNT(*) AS cnt FROM payments WHERE status='paid' GROUP BY payment_type");

// Event participation
$top_events = db_select($conn, "SELECT e.title, e.event_date, COUNT(er.id) AS joined
    FROM events e LEFT JOIN event_registrations er ON er.event_id=e.id GROUP BY e.id ORDER BY joined DESC LIMIT 5");

$page_title = 'Reports & Analytics';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Reports &amp; Analytics</h1>
      <p class="subtitle">Employment statistics, graduate population, and engagement.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
      <a href="<?= APP_URL ?>/actions/export_alumni.php" class="btn-secondary">
        <?= icon('document','w-4 h-4') ?>
        Export Alumni XLSX
      </a>
      <a href="<?= APP_URL ?>/actions/export_payments.php" class="btn-secondary">
        <?= icon('cash','w-4 h-4') ?>
        Export Payments XLSX
      </a>
      <a href="<?= APP_URL ?>/actions/export_tracer.php" class="btn-primary">
        <?= icon('chart','w-4 h-4') ?>
        Export Tracer XLSX
      </a>
    </div>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="stat-card">
      <div class="stat-label">Total Graduates</div>
      <div class="stat-value"><?= $total_grads ?></div>
      <div class="stat-sub">Issued Graduate IDs</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Alumni</div>
      <div class="stat-value"><?= $total_alumni ?></div>
      <div class="stat-sub">Tracked records</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Overall Employment Rate</div>
      <div class="stat-value text-emerald-700"><?= $emp_rate ?>%</div>
      <div class="stat-sub">Employed + self-employed</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Employed / Self-Employed</div>
      <div class="stat-value text-emerald-700"><?= $emp_employed + $emp_self ?></div>
      <div class="stat-sub">Active workforce</div>
    </div>
  </div>

  <div class="mt-6 grid lg:grid-cols-2 gap-4">
    <div class="card">
      <h3 class="font-bold text-ink-900 mb-3">Employment by Course</h3>
      <table class="table-clean">
        <thead><tr><th>Course</th><th>Alumni</th><th>Employed</th><th>Rate</th></tr></thead>
        <tbody>
          <?php foreach ($by_course as $c): $rate = $c['total']?round($c['employed']/$c['total']*100,1):0; ?>
            <tr>
              <td class="font-medium text-ink-800" data-label="Course"><?= e($c['course']) ?></td>
              <td data-label="Alumni"><?= (int)$c['total'] ?></td>
              <td data-label="Employed"><?= (int)$c['employed'] ?></td>
              <td data-label="Rate"><span class="font-semibold text-emerald-700"><?= $rate ?>%</span></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($by_course)): ?><tr><td colspan="4" class="text-center text-ink-400 py-4">No data.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h3 class="font-bold text-ink-900 mb-3">Graduate Population per Academic Year</h3>
      <table class="table-clean">
        <thead><tr><th>Academic Year</th><th>Graduates</th></tr></thead>
        <tbody>
          <?php foreach ($by_year as $y): ?>
            <tr><td class="font-medium text-ink-800" data-label="Academic Year"><?= e($y['academic_year']) ?></td><td data-label="Graduates"><?= (int)$y['total'] ?></td></tr>
          <?php endforeach; ?>
          <?php if (empty($by_year)): ?><tr><td colspan="2" class="text-center text-ink-400 py-4">No data.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h3 class="font-bold text-ink-900 mb-3">Payment Records Summary</h3>
      <table class="table-clean">
        <thead><tr><th>Type</th><th>Transactions</th><th>Total</th></tr></thead>
        <tbody>
          <?php foreach ($pay_by_type as $p): ?>
            <tr><td class="font-medium text-ink-800" data-label="Type"><?= e($p['payment_type']) ?></td><td data-label="Transactions"><?= (int)$p['cnt'] ?></td><td class="font-semibold text-ink-900" data-label="Total"><?= fmt_money($p['total']) ?></td></tr>
          <?php endforeach; ?>
          <?php if (empty($pay_by_type)): ?><tr><td colspan="3" class="text-center text-ink-400 py-4">No payments.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h3 class="font-bold text-ink-900 mb-3">Top Alumni Events Participation</h3>
      <table class="table-clean">
        <thead><tr><th>Event</th><th>Date</th><th>Registered</th></tr></thead>
        <tbody>
          <?php foreach ($top_events as $ev): ?>
            <tr><td class="font-medium text-ink-800" data-label="Event"><?= e($ev['title']) ?></td><td data-label="Date"><?= fmt_date($ev['event_date']) ?></td><td class="font-semibold text-ink-900" data-label="Registered"><?= (int)$ev['joined'] ?></td></tr>
          <?php endforeach; ?>
          <?php if (empty($top_events)): ?><tr><td colspan="3" class="text-center text-ink-400 py-4">No events.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
