<?php
// =============================================================
// Master Page Header (Top of every authenticated/public page)
// =============================================================
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

$page_title = $page_title ?? 'Dashboard';
$body_class = $body_class ?? '';
$hide_header = $hide_header ?? false;
$flashes = get_flashes();
$_role = function_exists('current_role') ? (current_role() ?? '') : '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title) ?> | <?= e(APP_NAME) ?></title>
  <link rel="icon" type="image/png" href="<?= APP_URL ?>/sksu1.png">

  <!-- Inter font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind via CDN. Green & White theme (palette name 'crimson' kept for back-compat with existing classes, values are green) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'],
          },
          colors: {
            crimson: {
              50:  '#f0fdf4',
              100: '#dcfce7',
              200: '#bbf7d0',
              300: '#86efac',
              400: '#4ade80',
              500: '#22c55e',
              600: '#16a34a',
              700: '#15803d',
              800: '#166534',
              900: '#14532d',
            },
            ink: {
              50:  '#f8fafc',
              100: '#f1f5f9',
              200: '#e2e8f0',
              300: '#cbd5e1',
              400: '#94a3b8',
              500: '#64748b',
              600: '#475569',
              700: '#334155',
              800: '#1e293b',
              900: '#0f172a',
            }
          },
          boxShadow: {
            'soft':  '0 1px 2px rgba(15,23,42,0.04), 0 1px 3px rgba(15,23,42,0.06)',
            'card':  '0 1px 2px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.04)',
            'pop':   '0 10px 30px -10px rgba(15,23,42,0.18)',
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/app.css">
</head>
<body class="font-sans bg-ink-50 min-h-screen text-ink-800 antialiased <?= e($body_class) ?>">

<!-- Top Navigation Bar -->
<?php if (!$hide_header): ?>
<header class="bg-white border-b border-ink-200 sticky top-0 z-30">
  <div class="max-w-[1400px] mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <button id="sidebarToggle" class="md:hidden text-ink-700 focus:outline-none focus:ring-2 focus:ring-crimson-500 rounded-md p-1.5" aria-label="Toggle navigation">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <a href="<?= APP_URL ?>/index.php" class="flex items-center gap-3 group">
        <img src="<?= APP_URL ?>/sksu1.png" alt="SKSU" class="w-10 h-10 rounded-full object-contain bg-white ring-1 ring-ink-200">
        <div class="leading-tight">
          <div class="font-bold text-[13.5px] sm:text-[14.5px] text-ink-900">SKSU Isulan</div>
          <div class="text-[10.5px] text-ink-500 hidden sm:block tracking-wide uppercase">Alumni Tracking System</div>
        </div>
      </a>
    </div>

    <div class="flex items-center gap-3 text-[12.5px]">
      <?php if (is_logged_in()): ?>
        <div class="hidden md:flex items-center gap-3 pl-3">
          <div class="text-right leading-tight">
            <div class="font-semibold text-ink-900 text-[12.5px]"><?= e($_SESSION['full_name']) ?></div>
            <div class="text-[10.5px] text-ink-500 capitalize">
              <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1 align-middle"></span><?= e($_role) ?>
            </div>
          </div>
          <div class="w-9 h-9 rounded-full bg-crimson-50 text-crimson-700 border border-crimson-100 flex items-center justify-center"><?= icon('user','w-5 h-5') ?></div>
        </div>
        <a href="<?= APP_URL ?>/logout.php" class="inline-flex items-center gap-1.5 text-ink-700 hover:text-crimson-700 hover:bg-crimson-50 border border-ink-200 hover:border-crimson-200 px-3 py-1.5 rounded-md font-medium transition">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
          <span>Logout</span>
        </a>
      <?php endif; ?>
    </div>
  </div>
</header>
<?php endif; ?>

<!-- SweetAlert2 flash messages -->
<?php if (!empty($flashes)): ?>
<script>
  window.AppFlashes = <?= json_encode($flashes, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>
<?php endif; ?>

<div class="flex max-w-[1400px] mx-auto">
