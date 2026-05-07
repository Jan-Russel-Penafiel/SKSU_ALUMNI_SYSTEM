<?php
// =============================================================
// Database Helper Functions (Procedural)
// =============================================================
require_once __DIR__ . '/config.php';

/**
 * Run a SELECT query with prepared statement and return all rows
 */
function db_select($conn, $sql, $types = '', $params = []) {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) return [];
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $rows;
}

/**
 * Run a SELECT query and return a single row
 */
function db_select_one($conn, $sql, $types = '', $params = []) {
    $rows = db_select($conn, $sql, $types, $params);
    return $rows[0] ?? null;
}

/**
 * Run an INSERT/UPDATE/DELETE query and return affected rows / insert id
 */
function db_execute($conn, $sql, $types = '', $params = []) {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) return false;
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    $ok = mysqli_stmt_execute($stmt);
    $insert_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    if (!$ok) return false;
    return $insert_id ?: true;
}

/**
 * Escape input for safe HTML output
 */
function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
