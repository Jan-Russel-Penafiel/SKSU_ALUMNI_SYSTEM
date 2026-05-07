<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) redirect('pages/' . current_role() . '/dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (authenticate($email, $password)) {
        flash('success', 'Welcome back, ' . $_SESSION['full_name'] . '!');
        redirect('pages/' . current_role() . '/dashboard.php');
    } else {
        flash('error', 'Invalid credentials. Please try again.');
    }
}
$page_title = 'Login';
include __DIR__ . '/templates/header.php';
?>
<main class="flex-1 flex items-center justify-center px-4 py-12">
  <div class="w-full max-w-md">
    <div class="bg-white rounded-2xl shadow-card border border-ink-200 p-8 relative overflow-hidden">
      <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-crimson-600 via-crimson-700 to-crimson-900"></div>

      <div class="flex items-center gap-3 mb-6">
        <div class="bg-gradient-to-br from-crimson-600 to-crimson-800 text-white rounded-xl w-12 h-12 flex items-center justify-center font-extrabold text-base shadow-soft">SK</div>
        <div>
          <h2 class="text-xl font-bold text-ink-900 tracking-tight">Welcome back</h2>
          <p class="text-xs text-ink-500">Sign in to <?= e(APP_NAME) ?></p>
        </div>
      </div>

      <form method="post" class="space-y-4">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div>
          <label class="label">Email address</label>
          <input type="email" name="email" class="input" required autofocus placeholder="you@sksu.edu.ph">
        </div>
        <div>
          <div class="flex items-center justify-between">
            <label class="label mb-0">Password</label>
            <span class="text-[11px] text-ink-400">Min. 6 characters</span>
          </div>
          <input type="password" name="password" class="input mt-1.5" required placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;">
        </div>
        <button type="submit" class="btn-primary w-full">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
          Sign in
        </button>
      </form>

      <div class="my-6 flex items-center gap-3 text-xs text-ink-400">
        <div class="flex-1 h-px bg-ink-200"></div>
        <span>OR</span>
        <div class="flex-1 h-px bg-ink-200"></div>
      </div>

      <p class="text-sm text-center text-ink-600">
        New graduating student? <a href="<?= APP_URL ?>/register.php" class="text-crimson-700 font-semibold hover:underline">Create an account</a>
      </p>
    </div>

    <div class="mt-5 rounded-xl border border-ink-200 bg-white px-4 py-3 text-xs text-ink-500 flex items-start gap-2">
      <svg class="w-4 h-4 mt-0.5 text-ink-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      <div>
        <div class="font-semibold text-ink-700">Default admin credentials</div>
        <div><code class="bg-ink-100 px-1 py-0.5 rounded text-ink-700">admin@sksu.edu.ph</code> / <code class="bg-ink-100 px-1 py-0.5 rounded text-ink-700">Admin@123</code></div>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__ . '/templates/footer.php'; ?>
