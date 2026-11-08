<?php
// Session Management & Authorization Helper (Strict English & Unified Login Version)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if a regular user is logged in
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']) && !isset($_SESSION['is_admin']);
}

/**
 * Require user login, redirecting to login.php if not authenticated
 */
function requireUserLogin() {
    if (!isUserLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        header("Location: /smart-gov-portal/login.php");
        exit;
    }
}

/**
 * Check if an administrator is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

/**
 * Require administrator login, redirecting to login.php?role=admin if not authenticated
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header("Location: /smart-gov-portal/login.php?role=admin");
        exit;
    }
}

/**
 * Clear session and log out
 */
function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
?>
