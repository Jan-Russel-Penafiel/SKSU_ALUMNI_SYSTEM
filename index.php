<?php
// =============================================================
// Landing Page / Router
// =============================================================
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    $role = current_role();
    redirect("pages/{$role}/dashboard.php");
}
$page_title = 'Welcome';
$body_class = 'overflow-hidden';
include __DIR__ . '/templates/header.php';
?>
<main class="flex-1 px-4 sm:px-6 relative flex items-center justify-center h-[calc(100vh-4rem)] overflow-hidden">
  <div class="absolute inset-x-0 top-0 h-[420px] bg-gradient-to-b from-crimson-50/60 to-transparent -z-10"></div>

  <div class="max-w-5xl mx-auto text-center w-full">
    <div class="inline-flex items-center gap-2 bg-white text-crimson-700 px-4 py-1.5 rounded-full text-xs font-semibold border border-crimson-200 shadow-soft">
      <span class="w-1.5 h-1.5 rounded-full bg-crimson-500"></span>
      Sultan Kudarat State University &middot; Isulan Campus
    </div>
    <h1 class="mt-6 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-ink-900 tracking-tight leading-[1.1]">
      Graduate-to-Alumni
      <span class="block bg-gradient-to-r from-crimson-700 to-crimson-900 bg-clip-text text-transparent">Tracking System</span>
    </h1>
    <p class="mt-5 text-base sm:text-lg text-ink-600 max-w-2xl mx-auto leading-relaxed">
      An integrated platform managing the full graduate lifecycle &mdash; from graduation processing and registrar validation to alumni engagement and tracer monitoring.
    </p>
    <div class="mt-8 flex justify-center gap-3 flex-wrap">
      <a href="<?= APP_URL ?>/register.php" class="btn-primary px-6 py-3 text-[15px] shadow-pop">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
        Register as Graduating Student
      </a>
      <a href="<?= APP_URL ?>/login.php" class="btn-secondary px-6 py-3 text-[15px]">Sign in</a>
    </div>
  </div>

</main>
<?php include __DIR__ . '/templates/footer.php'; ?>
