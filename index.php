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
include __DIR__ . '/templates/header.php';
?>
<main class="flex-1 px-4 sm:px-6 py-16 relative">
  <div class="absolute inset-x-0 top-0 h-[420px] bg-gradient-to-b from-crimson-50/60 to-transparent -z-10"></div>

  <div class="max-w-5xl mx-auto text-center">
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

  <div class="mt-16 max-w-6xl mx-auto grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
    <?php
    $features = [
      ['cap','Student Module','Register, upload requirements, schedule and pay.'],
      ['building','Registrar Module','Verify, validate and approve graduate records.'],
      ['briefcase','Alumni Module','Track employment and engage with events.'],
      ['chart','Reports & Analytics','Tracer monitoring and employment statistics.'],
    ];
    foreach ($features as [$ic,$t,$d]): ?>
      <div class="card group cursor-default">
        <div class="inline-flex items-center justify-center w-11 h-11 rounded-lg bg-crimson-50 text-crimson-700 border border-crimson-100 group-hover:bg-crimson-100 transition"><?= icon($ic,'w-6 h-6') ?></div>
        <div class="mt-4 font-bold text-ink-900"><?= $t ?></div>
        <p class="text-sm text-ink-500 mt-1.5 leading-relaxed"><?= $d ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="mt-12 max-w-6xl mx-auto rounded-2xl border border-ink-200 bg-white p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 shadow-soft">
    <div>
      <div class="text-xs font-semibold uppercase tracking-[0.1em] text-crimson-700">Get started</div>
      <div class="mt-1 text-lg font-bold text-ink-900">Already a student or registrar?</div>
      <div class="text-sm text-ink-500">Sign in to access your dashboard, requirements, and alumni records.</div>
    </div>
    <a href="<?= APP_URL ?>/login.php" class="btn-primary">Sign in to your account</a>
  </div>
</main>
<?php include __DIR__ . '/templates/footer.php'; ?>
