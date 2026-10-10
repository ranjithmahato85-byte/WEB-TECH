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

/**
 * Demo credentials that were seeded by the original Flask application and
 * therefore exist only as Werkzeug scrypt hashes (which PHP cannot verify).
 * On a successful match the hash is transparently upgraded to a PHP hash.
 */
const LEGACY_DEMO_CREDENTIALS = [
    'admin' => 'admin123',
    'ranjeet' => 'password123',
    'bikash' => 'password123',
    'pooja' => 'password123',
    'binod' => 'password123',
    'sitamaya' => 'password123',
];

function verify_user_password(string $plain, string $storedHash, array &$user): bool {
    // 1. Standard PHP password_verify (Bcrypt, Argon2)
    if (password_verify($plain, $storedHash)) {
        return true;
    }

    // 2. Legacy Werkzeug scrypt hash left over from the Python/SQLAlchemy schema.
    //    PHP has no native scrypt support, so only the documented demo accounts
    //    can be matched; any other legacy account must reset its password.
    if (str_starts_with($storedHash, 'scrypt:')) {
        $username = strtolower($user['username'] ?? '');
        $expected = LEGACY_DEMO_CREDENTIALS[$username] ?? null;

        if ($expected !== null && hash_equals($expected, $plain)) {
            // Upgrade the stored hash to a native PHP password_hash
            $newHash = hash_password($plain);
            $db = get_db();
            $stmt = $db->prepare("UPDATE users SET password_hash = :h WHERE id = :id");
            $stmt->execute([':h' => $newHash, ':id' => $user['id']]);
            $user['password_hash'] = $newHash;
            return true;
        }
    }

    return false;
}
