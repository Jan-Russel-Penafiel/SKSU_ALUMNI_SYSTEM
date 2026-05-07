<?php
// =============================================================
// Database Configuration & Global Settings
// =============================================================

// Application Settings
define('APP_NAME', 'SKSU Isulan - Graduate to Alumni Tracking System');
define('APP_URL', 'http://localhost/alumni');
define('APP_TIMEZONE', 'Asia/Manila');

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'sksu_alumni');
define('DB_USER', 'root');
define('DB_PASS', '');

// File Upload Paths
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');
define('UPLOAD_URL', APP_URL . '/assets/uploads/');

// Set Timezone
date_default_timezone_set(APP_TIMEZONE);

// Establish MySQLi Connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die('Database connection failed: ' . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');
