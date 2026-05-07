<?php
// =============================================================
// Setup Script - Run ONCE to install database
// Open: http://localhost/alumni/setup.php
// =============================================================
require_once __DIR__ . '/includes/config.php';

// Reconnect without selecting a DB so we can create it
$root = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
if (!$root) {
    die('Connection failed: ' . mysqli_connect_error());
}

$schema_sql = file_get_contents(__DIR__ . '/database/schema.sql');
if (!$schema_sql) die('schema.sql not found.');

// Execute the schema (multi-query)
if (!mysqli_multi_query($root, $schema_sql)) {
    die('Schema error: ' . mysqli_error($root));
}
// Drain results
while (mysqli_more_results($root) && mysqli_next_result($root)) { ; }
mysqli_close($root);

// Reconnect to the freshly created DB and update demo passwords with real bcrypt hashes
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$admin_hash = password_hash('Admin@123', PASSWORD_BCRYPT);
$reg_hash   = password_hash('Registrar@123', PASSWORD_BCRYPT);
mysqli_query($conn, "UPDATE users SET password='" . mysqli_real_escape_string($conn, $admin_hash) . "' WHERE email='admin@sksu.edu.ph'");
mysqli_query($conn, "UPDATE users SET password='" . mysqli_real_escape_string($conn, $reg_hash)   . "' WHERE email='registrar@sksu.edu.ph'");
mysqli_close($conn);

?>
<!doctype html>
<html lang="en"><head>
<meta charset="utf-8">
<title>Setup — SKSU Alumni System</title>
<script src="https://cdn.tailwindcss.com"></script>
</head><body class="bg-gray-50 min-h-screen flex items-center justify-center">
<div class="max-w-lg w-full bg-white rounded-2xl shadow-lg border-t-4 border-red-700 p-8">
  <h1 class="text-2xl font-bold text-red-700 flex items-center gap-2">
    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    Setup Complete
  </h1>
  <p class="mt-2 text-gray-700">Database <code class="bg-gray-100 px-1 rounded">sksu_alumni</code> has been created and seeded.</p>
  <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4 text-sm">
    <p class="font-semibold text-red-800">Default credentials:</p>
    <ul class="mt-2 space-y-1 text-gray-700">
      <li>Admin — <code>admin@sksu.edu.ph</code> / <code>Admin@123</code></li>
      <li>Registrar — <code>registrar@sksu.edu.ph</code> / <code>Registrar@123</code></li>
    </ul>
  </div>
  <a href="<?= APP_URL ?>/login.php" class="mt-6 block text-center bg-red-700 hover:bg-red-800 text-white font-semibold py-2.5 px-4 rounded-lg">Go to Login</a>
  <p class="mt-3 text-xs text-gray-500 text-center">For security, delete <code>setup.php</code> after first run.</p>
</div>
</body></html>
