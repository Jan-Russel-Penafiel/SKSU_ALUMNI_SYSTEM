<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_role('student');

$uid = current_user_id();
$all_payments = db_select($conn, "SELECT * FROM payments WHERE user_id=? ORDER BY id ASC", 'i', [$uid]);
$pg = paginate($all_payments, 10);
$payments = $pg['rows'];
$total = db_select_one($conn, "SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE user_id=? AND status='paid'", 'i', [$uid]);

$page_title = 'Payments';
include __DIR__ . '/../../templates/header.php';
include __DIR__ . '/../../templates/sidebar.php';
?>
<main class="flex-1 px-4 sm:px-6 py-8">
  <div class="page-head">
    <div>
      <h1>Payment Records</h1>
      <p class="subtitle">Approved total paid: <span class="font-bold text-emerald-700"><?= fmt_money($total['s']) ?></span></p>
    </div>
    <button type="button" class="btn-primary" data-modal-open="paymentModal">
      <?= icon('cash','w-4 h-4') ?>
      Submit payment
    </button>
  </div>

  <div class="table-wrap has-pagination overflow-x-auto">
    <table class="table-clean">
      <thead>
        <tr>
          <th class="pl-6">Type</th>
          <th>Amount</th>
          <th>Reference</th>
          <th>Method</th>
          <th>Date</th>
          <th>Status</th>
          <th class="pr-6 text-right">Receipt</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($payments as $p): ?>
          <tr>
            <td class="pl-6 font-medium text-ink-800" data-label="Type"><?= e($p['payment_type']) ?></td>
            <td class="font-semibold text-ink-900" data-label="Amount"><?= fmt_money($p['amount']) ?></td>
            <td class="font-mono text-xs text-ink-600" data-label="Reference"><?= e($p['reference_no']) ?></td>
            <td class="text-ink-600" data-label="Method"><?= e($p['payment_method']) ?></td>
            <td class="text-xs text-ink-500" data-label="Date"><?= $p['paid_at'] ? fmt_datetime($p['paid_at']) : ($p['status'] === 'pending' ? 'Pending review' : 'Not approved') ?></td>
            <td data-label="Status"><?= status_badge($p['status']) ?></td>
            <td class="pr-6 text-right" data-label="Receipt">
              <?php if ($p['status'] === 'paid'): ?>
                <a href="<?= APP_URL ?>/actions/receipt.php?id=<?= (int)$p['id'] ?>" target="_blank" class="text-xs font-semibold text-crimson-700 hover:bg-crimson-50 px-2 py-1 rounded-md">Receipt</a>
              <?php else: ?>
                <span class="text-xs text-ink-400">Unavailable</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($payments)): ?>
          <tr><td colspan="7"><div class="empty-state">
            <div class="empty-icon"><?= icon('cash','w-5 h-5') ?></div>
            <div>No payments recorded yet.</div>
          </div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?= render_pagination($pg) ?>
</main>

<!-- Submit Payment Modal -->
<div id="paymentModal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="paymentModalTitle">
  <div class="modal-panel" role="document">
    <div class="modal-head">
      <div>
        <h3 id="paymentModalTitle">Submit payment</h3>
        <div class="modal-sub">A reference number will be generated automatically and sent for registrar review.</div>
      </div>
      <button type="button" class="modal-close" data-modal-close aria-label="Close">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <form action="<?= APP_URL ?>/actions/student_record_payment.php" method="post">
      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
      <div class="modal-body grid sm:grid-cols-2 gap-4">
        <div><label class="label">Type</label>
          <select name="payment_type" class="input" required>
            <?php foreach (app_payment_type_options() as $paymentType): ?>
              <option value="<?= e($paymentType) ?>"><?= e($paymentType) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label class="label">Amount (&#8369;)</label><input type="number" step="0.01" name="amount" class="input" required></div>
        <div class="sm:col-span-2"><label class="label">Payment method</label>
          <select name="payment_method" class="input">
            <?php foreach (app_payment_method_options() as $paymentMethod): ?>
              <option value="<?= e($paymentMethod) ?>"><?= e($paymentMethod) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="sm:col-span-2"><label class="label">Remarks</label><textarea name="remarks" class="input" rows="3"></textarea></div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-secondary" data-modal-close>Cancel</button>
        <button type="submit" class="btn-primary">
          <?= icon('cash','w-4 h-4') ?>
          Submit for review
        </button>
      </div>
    </form>
  </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
