<?php
// =============================================================
// Authentication & Session Helpers
// =============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Check if a user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user role
 */
function current_role() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user id
 */
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user data
 */
function current_user() {
    global $conn;
    if (!is_logged_in()) return null;
    return db_select_one($conn, "SELECT * FROM users WHERE id=?", 'i', [current_user_id()]);
}

/**
 * Require login - redirects to login page if not logged in
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . APP_URL . '/login.php');
        exit;
    }
}

/**
 * Require a specific role
 */
function require_role($roles) {
    require_login();
    if (!is_array($roles)) $roles = [$roles];
    if (!in_array(current_role(), $roles, true)) {
        http_response_code(403);
        die('Forbidden — your role does not have access to this page.');
    }
}

/**
 * Authenticate user with email/password
 */
function authenticate($email, $password) {
    global $conn;
    $user = db_select_one($conn, "SELECT * FROM users WHERE email=? AND status='active'", 's', [$email]);
    if (!$user) return false;
    if (!password_verify($password, $user['password'])) return false;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    return $user;
}

/**
 * Log out
 */
function logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']);
    }
    session_destroy();
}

/**
 * Set a flash message
 */
function flash($type, $message) {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * Get all flash messages and clear them
 */
function get_flashes() {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

/**
 * Generate a unique ID with prefix
 */
function generate_unique_id($prefix) {
    return $prefix . date('Y') . '-' . str_pad((string)mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * Generate a payment reference number
 */
function generate_reference() {
    return 'REF-' . date('YmdHis') . '-' . mt_rand(100, 999);
}

/**
 * CSRF helpers (simple)
 */
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_check($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
}
