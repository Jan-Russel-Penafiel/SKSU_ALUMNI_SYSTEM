<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('registrar');

$status_filter = $_GET['status'] ?? 'pending';
$valid_filters = array_merge(['all'], app_payment_status_options());
if (!in_array($status_filter, $valid_filters, true)) $status_filter = 'pending';

$conds = ['1'];
$types = '';
$params = [];
if ($status_filter !== 'all') {
    $conds[] = 'p.status=?';
    $types .= 's';
    $params[] = $status_filter;
}
$where = implode(' AND ', $conds);

$all_rows = db_select($conn,
    "SELECT p.*, u.full_name, u.email
     FROM payments p
     JOIN users u ON u.id=p.user_id
     WHERE $where
     ORDER BY CASE p.status WHEN 'pending' THEN 0 WHEN 'paid' THEN 1 WHEN 'rejected' THEN 2 ELSE 3 END, p.id DESC",
    $types,
    $params
);
$pg = paginate($all_rows, 10);
$rows = $pg['rows'];

$total = db_select_one($conn, "SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE status='paid'");
$pending_count = db_count($conn, 'payments', "status='pending'");

$page_title = 'Payment Review';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Payment Review</h1>
      <p class="subtitle">
        Approved revenue: <span class="font-bold text-emerald-700"><?= fmt_money($total['s']) ?></span>
        <span class="mx-1 text-ink-300">/</span>
        Pending review: <span class="font-bold text-amber-700"><?= (int)$pending_count ?></span>
      </p>
    </div>
    <a href="<?= APP_URL ?>/actions/export_payments.php" class="btn-secondary">
      <?= icon('document','w-4 h-4') ?>
      Export XLSX
    </a>
  </div>

  <div class="mb-4 flex gap-2 flex-wrap">
    <?php foreach ($valid_filters as $filter):
      $active = $filter === $status_filter;
      $label = $filter === 'all' ? 'All' : ucfirst($filter);
    ?>
      <a href="?status=<?= e($filter) ?>" class="px-3 py-1.5 rounded-md text-xs font-semibold transition <?= $active ? 'bg-crimson-700 text-white' : 'bg-white text-ink-700 border border-ink-200 hover:bg-ink-50' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
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
          <th>Status</th>
          <th class="pr-6 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $p): ?>
          <tr>
            <td class="pl-6 font-mono text-xs text-ink-600" data-label="Reference"><?= e($p['reference_no']) ?></td>
            <td class="font-medium text-ink-900" data-label="Payer">
              <div><?= e($p['full_name']) ?></div>
              <div class="text-xs text-ink-500"><?= e($p['email']) ?></div>
            </td>
            <td class="text-ink-700" data-label="Type"><?= e($p['payment_type']) ?></td>
            <td class="font-semibold text-ink-900" data-label="Amount"><?= fmt_money($p['amount']) ?></td>
            <td class="text-ink-600" data-label="Method"><?= e($p['payment_method']) ?></td>
            <td class="text-xs text-ink-500" data-label="Date"><?= $p['paid_at'] ? fmt_datetime($p['paid_at']) : ($p['status'] === 'pending' ? 'Pending review' : 'Not approved') ?></td>
            <td data-label="Status"><?= status_badge($p['status']) ?></td>
            <td class="pr-6 text-right" data-label="Actions">
              <?php if ($p['status'] === 'pending'): ?>
                <div class="inline-flex gap-1 flex-wrap justify-end">
                  <form action="<?= APP_URL ?>/actions/registrar_update_payment.php" method="post" class="inline">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <input type="hidden" name="action" value="approve">
                    <button type="submit" data-confirm="Approve this payment and mark it paid?" class="text-xs font-semibold text-emerald-700 hover:bg-emerald-50 px-2 py-1 rounded-md">Approve</button>
                  </form>
                  <form action="<?= APP_URL ?>/actions/registrar_update_payment.php" method="post" class="inline">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                    <input type="hidden" name="action" value="disapprove">
                    <button type="submit" data-confirm="Disapprove this payment?" data-confirm-button="Disapprove" class="text-xs font-semibold text-rose-700 hover:bg-rose-50 px-2 py-1 rounded-md">Disapprove</button>
                  </form>
                </div>
              <?php elseif ($p['status'] === 'paid'): ?>
                <a href="<?= APP_URL ?>/actions/receipt.php?id=<?= (int)$p['id'] ?>" target="_blank" class="text-xs font-semibold text-crimson-700 hover:bg-crimson-50 px-2 py-1 rounded-md">Receipt</a>
              <?php else: ?>
                <span class="text-xs text-ink-400">Reviewed</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="8"><div class="empty-state">
            <div class="empty-icon"><?= icon('cash','w-5 h-5') ?></div>
            <div>No payments match this filter.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
