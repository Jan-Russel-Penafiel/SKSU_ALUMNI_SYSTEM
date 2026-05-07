<?php
// =============================================================
// General Helper Functions
// =============================================================
require_once __DIR__ . '/icons.php';
$__autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($__autoload)) require_once $__autoload;

/**
 * Stream rows as a real .xlsx download via PhpSpreadsheet.
 * $headers: list of column header strings.
 * $rows: list of row arrays (assoc or numeric).
 * $filename: output filename (without extension).
 * $sheetTitle: worksheet title (max 31 chars, no special chars).
 */
function send_xlsx(array $headers, array $rows, string $filename, string $sheetTitle = 'Sheet1'): void {
    $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $ss->getActiveSheet();
    $safeTitle = preg_replace('/[\\\\\/\*\[\]\:\?]/', ' ', $sheetTitle);
    $sheet->setTitle(mb_substr($safeTitle, 0, 31));

    $colCount = count($headers);
    $sheet->fromArray($headers, null, 'A1');
    $headerRange = 'A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colCount) . '1';
    $sheet->getStyle($headerRange)->getFont()->setBold(true);
    $sheet->getStyle($headerRange)->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setRGB('E2E8F0');
    $sheet->freezePane('A2');

    $r = 2;
    foreach ($rows as $row) {
        $sheet->fromArray(array_values($row), null, 'A' . $r);
        $r++;
    }
    for ($i = 1; $i <= $colCount; $i++) {
        $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
    }

    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '.xlsx"');
    header('Cache-Control: max-age=0');
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($ss, 'Xlsx');
    $writer->save('php://output');
    exit;
}

/**
 * Format date to "Month d, Y"
 */
function fmt_date($date) {
    if (!$date) return '';
    return date('M d, Y', strtotime($date));
}

/**
 * Format datetime to "Month d, Y h:i A"
 */
function fmt_datetime($datetime) {
    if (!$datetime) return '';
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

/**
 * Paginate an in-memory result set.
 * Returns ['rows' => slice, 'page' => int, 'pages' => int, 'total' => int, 'per_page' => int, 'from' => int, 'to' => int]
 */
function paginate(array $rows, int $per_page = 10, string $param = 'page'): array {
    $total = count($rows);
    $pages = max(1, (int)ceil($total / max(1, $per_page)));
    $page  = max(1, min($pages, (int)($_GET[$param] ?? 1)));
    $start = ($page - 1) * $per_page;
    $slice = array_slice($rows, $start, $per_page);
    return [
        'rows'     => $slice,
        'page'     => $page,
        'pages'    => $pages,
        'total'    => $total,
        'per_page' => $per_page,
        'from'     => $total ? $start + 1 : 0,
        'to'       => $start + count($slice),
        'param'    => $param,
    ];
}

/**
 * Render the pagination strip for a paginate() result.
 * Preserves existing query string, swapping only the page parameter.
 * Hidden entirely when fewer than 10 records exist (single page of records).
 */
function render_pagination(array $p): string {
    if (($p['total'] ?? 0) < 10) return '';
    $page  = $p['page']; $pages = $p['pages']; $param = $p['param'];
    $total = $p['total']; $from = $p['from']; $to = $p['to'];
    $qs = $_GET; unset($qs[$param]);
    $base = $_SERVER['PHP_SELF'] . '?' . http_build_query($qs);
    if (substr($base, -1) !== '?' && substr($base, -1) !== '&' && !empty($qs)) $base .= '&';
    if (empty($qs)) $base = $_SERVER['PHP_SELF'] . '?';
    $link = function ($n, $label = null, $active = false, $disabled = false) use ($base, $param) {
        $cls = 'pg-link' . ($active ? ' is-active' : '') . ($disabled ? ' is-disabled' : '');
        $lbl = $label ?? (string)$n;
        if ($disabled || $active) return '<span class="' . $cls . '">' . $lbl . '</span>';
        return '<a class="' . $cls . '" href="' . e($base . $param . '=' . $n) . '">' . $lbl . '</a>';
    };
    $items = [];
    $items[] = $link(max(1, $page - 1), '&laquo;', false, $page <= 1);
    // build a windowed list
    $window = 1;
    $shown = [];
    for ($i = 1; $i <= $pages; $i++) {
        if ($i === 1 || $i === $pages || ($i >= $page - $window && $i <= $page + $window)) {
            $shown[] = $i;
        }
    }
    $prev = 0;
    foreach ($shown as $i) {
        if ($prev && $i - $prev > 1) $items[] = '<span class="pg-ellipsis">&hellip;</span>';
        $items[] = $link($i, (string)$i, $i === $page);
        $prev = $i;
    }
    $items[] = $link(min($pages, $page + 1), '&raquo;', false, $page >= $pages);

    $summary = $total
        ? 'Showing <strong>' . $from . '</strong> to <strong>' . $to . '</strong> of <strong>' . $total . '</strong>'
        : 'No records';

    return '<div class="pagination">'
         . '<div class="pg-summary">' . $summary . '</div>'
         . '<div class="pg-list">' . implode('', $items) . '</div>'
         . '</div>';
}
