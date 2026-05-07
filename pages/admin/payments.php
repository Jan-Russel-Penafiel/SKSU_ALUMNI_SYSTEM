<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('admin');

$rows = db_select($conn, "SELECT p.*, u.full_name FROM payments p JOIN users u ON u.id=p.user_id ORDER BY p.paid_at DESC");
$total = db_select_one($conn, "SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE status='paid'");

$page_title = 'Payments';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <h1 class="text-2xl font-bold text-gray-900">All Payments</h1>
  <p class="text-sm text-gray-500">Total revenue collected: <span class="font-bold text-emerald-600"><?= fmt_money($total['s']) ?></span></p>

  <div class="mt-6 card overflow-x-auto">
    <table class="table-clean">
      <thead><tr><th>Reference</th><th>Payer</th><th>Type</th><th>Amount</th><th>Method</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach ($rows as $p): ?>
          <tr>
            <td class="font-mono text-xs"><?= e($p['reference_no']) ?></td>
            <td><?= e($p['full_name']) ?></td>
            <td><?= e($p['payment_type']) ?></td>
            <td class="font-semibold"><?= fmt_money($p['amount']) ?></td>
            <td><?= e($p['payment_method']) ?></td>
            <td class="text-xs"><?= fmt_datetime($p['paid_at']) ?></td>
            <td><?= status_badge($p['status']) ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="7" class="text-center text-gray-400 py-6">No payments yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/../../templates/footer.php'; ?>
