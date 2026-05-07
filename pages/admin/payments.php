<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('admin');

$all_rows = db_select($conn, "SELECT p.*, u.full_name FROM payments p JOIN users u ON u.id=p.user_id ORDER BY p.id ASC");
$pg = paginate($all_rows, 10);
$rows = $pg['rows'];

$total = db_select_one($conn, "SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE status='paid'");

$page_title = 'Payments';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>All Payments</h1>
      <p class="subtitle">Total revenue collected: <span class="font-bold text-emerald-700"><?= fmt_money($total['s']) ?></span></p>
    </div>
    <a href="<?= APP_URL ?>/actions/export_payments.php" class="btn-secondary">
      <?= icon('document','w-4 h-4') ?>
      Export XLSX
    </a>
  </div>

  <div class="table-wrap has-pagination overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Reference</th>
          <th>Payer</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Method</th>
          <th>Date</th>
          <th class="pr-6">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $p): ?>
          <tr>
            <td class="pl-6 font-mono text-xs text-ink-600" data-label="Reference"><?= e($p['reference_no']) ?></td>
            <td class="font-medium text-ink-900" data-label="Payer"><?= e($p['full_name']) ?></td>
            <td class="text-ink-700" data-label="Type"><?= e($p['payment_type']) ?></td>
            <td class="font-semibold text-ink-900" data-label="Amount"><?= fmt_money($p['amount']) ?></td>
            <td class="text-ink-600" data-label="Method"><?= e($p['payment_method']) ?></td>
            <td class="text-xs text-ink-500" data-label="Date"><?= fmt_datetime($p['paid_at']) ?></td>
            <td class="pr-6" data-label="Status"><?= status_badge($p['status']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="7"><div class="empty-state">
            <div class="empty-icon"><?= icon('cash','w-5 h-5') ?></div>
            <div>No payments yet.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
