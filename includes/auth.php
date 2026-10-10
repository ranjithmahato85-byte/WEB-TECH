<?php
/**
 * Nepal Online Voting System
 * Authentication & Session Management
 */

if (session_status() === PHP_SESSION_NONE) {
    // Configure session security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

require_once __DIR__ . '/models.php';

function current_user() {
    static $user = null;
    if ($user !== null) return $user;
    if (!empty($_SESSION['user_id'])) {
        $user = user_find_by_id($_SESSION['user_id']);
        if (!$user || !$user['is_active']) {
            user_logout();
            $user = null;
        }
    }
    return $user;
}

function is_logged_in(): bool {
    return current_user() !== null;
}

function is_admin(): bool {
    $u = current_user();
    return $u && $u['role'] === 'admin';
}

function login_required($redirectUrl = null) {
    if (!is_logged_in()) {
        $target = $redirectUrl ?: url_for('login');
        flash('Please log in to access this page.', 'warning');
        redirect($target . '?next=' . urlencode($_SERVER['REQUEST_URI'] ?? '/'));
    }
}

function admin_required() {
    if (!is_logged_in()) {
        flash('Admin authentication required.', 'error');
        redirect(url_for('admin_login'));
    }
    if (!is_admin()) {
        flash('Access denied. Administrator privileges required.', 'error');
        redirect(url_for('voter_dashboard'));
    }
}

function user_login(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
}

function user_logout(): void {
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

function hash_password(string $plain): string {
    return password_hash($plain, PASSWORD_DEFAULT);
}

function verify_user_password(string $plain, string $storedHash, array &$user): bool {
    return password_verify($plain, $storedHash);
}
