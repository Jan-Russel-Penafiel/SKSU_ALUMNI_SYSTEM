<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$payment = db_select_one($conn, "SELECT p.*, u.full_name, u.email FROM payments p JOIN users u ON u.id=p.user_id WHERE p.id=?", 'i', [$id]);
if (!$payment) { http_response_code(404); die('Receipt not found.'); }
// Permission: payer or admin/registrar
if ((int)$payment['user_id'] !== current_user_id() && !in_array(current_role(), ['admin','registrar'], true)) {
    http_response_code(403); die('Forbidden.');
}
?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><title>Receipt | <?= e($payment['reference_no']) ?></title>
<script src="https://cdn.tailwindcss.com"></script>
</head><body class="bg-gray-100 p-8">
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-2xl border-t-8 border-red-700 p-8 print:shadow-none print:border">
  <div class="flex justify-between items-start">
    <div>
      <div class="text-2xl font-bold text-red-700">SKSU Isulan Campus</div>
      <div class="text-sm text-gray-500">Alumni Tracking System Official Digital Receipt</div>
    </div>
    <div class="text-right">
      <div class="text-xs text-gray-500">Reference</div>
      <div class="font-mono font-bold text-red-700"><?= e($payment['reference_no']) ?></div>
    </div>
  </div>
  <hr class="my-4">
  <div class="grid grid-cols-2 gap-3 text-sm">
    <div><div class="text-gray-500">Payer</div><div class="font-semibold"><?= e($payment['full_name']) ?></div></div>
    <div><div class="text-gray-500">Email</div><div><?= e($payment['email']) ?></div></div>
    <div><div class="text-gray-500">Type</div><div><?= e($payment['payment_type']) ?></div></div>
    <div><div class="text-gray-500">Method</div><div><?= e($payment['payment_method']) ?></div></div>
    <div><div class="text-gray-500">Date</div><div><?= fmt_datetime($payment['paid_at']) ?></div></div>
    <div><div class="text-gray-500">Status</div><div class="uppercase font-semibold text-emerald-600"><?= e($payment['status']) ?></div></div>
  </div>
  <div class="mt-6 text-right">
    <div class="text-sm text-gray-500">Amount Paid</div>
    <div class="text-3xl font-bold text-red-700"><?= fmt_money($payment['amount']) ?></div>
  </div>
  <?php if ($payment['remarks']): ?>
    <div class="mt-4 bg-gray-50 border-l-4 border-gray-300 p-3 text-xs text-gray-600">
      <strong>Remarks:</strong> <?= e($payment['remarks']) ?>
    </div>
  <?php endif; ?>
  <hr class="my-6">
  <div class="text-xs text-center text-gray-500">
    This is a computer-generated digital receipt.<br>For inquiries, contact registrar@sksu.edu.ph.
  </div>
  <div class="mt-6 text-center print:hidden">
    <button onclick="window.print()" class="bg-red-700 hover:bg-red-800 text-white font-semibold px-6 py-2 rounded-lg">Print Receipt</button>
    <a href="javascript:history.back()" class="ml-2 bg-gray-200 hover:bg-gray-300 px-6 py-2 rounded-lg font-semibold">Back</a>
  </div>
</div>
</body></html>
