</div><!-- /.flex -->

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
