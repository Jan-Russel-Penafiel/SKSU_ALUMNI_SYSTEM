<?php
// =============================================================
// General Helper Functions
// =============================================================
require_once __DIR__ . '/icons.php';

/**
 * Format date to "Month d, Y"
 */
function fmt_date($date) {
    if (!$date) return '—';
    return date('M d, Y', strtotime($date));
}

/**
 * Format datetime to "Month d, Y h:i A"
 */
function fmt_datetime($datetime) {
    if (!$datetime) return '—';
    return date('M d, Y h:i A', strtotime($datetime));
}

/**
 * Format Philippine peso amount
 */
function fmt_money($amount) {
    return '₱ ' . number_format((float)$amount, 2);
}

/**
 * Render a status badge with Tailwind classes
 */
function status_badge($status) {
    $status = strtolower((string)$status);
    $map = [
        'pending'        => ['bg-amber-50',   'text-amber-700',   'border-amber-200',   '#f59e0b'],
        'approved'       => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', '#10b981'],
        'rejected'       => ['bg-rose-50',    'text-rose-700',    'border-rose-200',    '#f43f5e'],
        'active'         => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', '#10b981'],
        'inactive'       => ['bg-slate-100',  'text-slate-700',   'border-slate-200',   '#94a3b8'],
        'paid'           => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', '#10b981'],
        'refunded'       => ['bg-sky-50',     'text-sky-700',     'border-sky-200',     '#0ea5e9'],
        'scheduled'      => ['bg-sky-50',     'text-sky-700',     'border-sky-200',     '#0ea5e9'],
        'attended'       => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', '#10b981'],
        'cancelled'      => ['bg-rose-50',    'text-rose-700',    'border-rose-200',    '#f43f5e'],
        'employed'       => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', '#10b981'],
        'unemployed'     => ['bg-rose-50',    'text-rose-700',    'border-rose-200',    '#f43f5e'],
        'self-employed'  => ['bg-indigo-50',  'text-indigo-700',  'border-indigo-200',  '#6366f1'],
        'further studies'=> ['bg-violet-50',  'text-violet-700',  'border-violet-200',  '#8b5cf6'],
        'upcoming'       => ['bg-sky-50',     'text-sky-700',     'border-sky-200',     '#0ea5e9'],
        'ongoing'        => ['bg-amber-50',   'text-amber-700',   'border-amber-200',   '#f59e0b'],
        'completed'      => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200', '#10b981'],
    ];
    [$bg, $tx, $br, $dot] = $map[$status] ?? ['bg-slate-50','text-slate-700','border-slate-200','#94a3b8'];
    $label = e(ucfirst($status));
    return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold tracking-wide border '
        . $bg . ' ' . $tx . ' ' . $br . '">'
        . '<span class="w-1.5 h-1.5 rounded-full" style="background:' . $dot . '"></span>'
        . $label . '</span>';
}

/**
 * Safe file upload helper
 */
function handle_upload($file_input, $sub_dir, $allowed_ext = ['pdf','jpg','jpeg','png','doc','docx']) {
    if (!isset($_FILES[$file_input]) || $_FILES[$file_input]['error'] !== UPLOAD_ERR_OK) {
        return [false, 'No file uploaded or upload error.'];
    }
    $file = $_FILES[$file_input];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_ext, true)) {
        return [false, 'File type not allowed. Allowed: ' . implode(', ', $allowed_ext)];
    }
    if ($file['size'] > 10 * 1024 * 1024) {
        return [false, 'File too large. Max 10 MB.'];
    }
    $target_dir = UPLOAD_PATH . $sub_dir . '/';
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $filename = uniqid('f_') . '.' . $ext;
    $target_path = $target_dir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $target_path)) {
        return [false, 'Failed to move uploaded file.'];
    }
    return [true, 'uploads/' . $sub_dir . '/' . $filename];
}

/**
 * Safe redirect helper
 */
function redirect($path) {
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

/**
 * Get total count from a table with optional filter
 */
function db_count($conn, $table, $where = '1', $types = '', $params = []) {
    $sql = "SELECT COUNT(*) AS cnt FROM {$table} WHERE {$where}";
    $row = db_select_one($conn, $sql, $types, $params);
    return (int)($row['cnt'] ?? 0);
}
