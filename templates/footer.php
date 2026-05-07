</div><!-- /.flex -->

<footer class="bg-white border-t border-ink-200 mt-12">
  <div class="max-w-[1400px] mx-auto px-4 sm:px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-ink-500">
    <div>&copy; <?= date('Y') ?> Sultan Kudarat State University &mdash; Isulan Campus.</div>
    <div class="flex items-center gap-3">
      <span>Alumni Tracking System</span>
      <span class="w-1 h-1 rounded-full bg-ink-300"></span>
      <span class="text-ink-400">v1.0</span>
    </div>
  </div>
</footer>

<script>
  // Mobile sidebar toggle
  const toggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', () => sidebar.classList.toggle('hidden'));
  }
</script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
