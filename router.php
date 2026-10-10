<?php
/**
 * Router for PHP's built-in development server.
 *
 * Usage:  php -S 0.0.0.0:8080 router.php
 *
 * Real files (CSS, JS, images, uploads) are served directly; every other
 * request is handed off to the front controller, mirroring the .htaccess
 * rewrite rules used under Apache/XAMPP.
 */

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false; // Let the built-in server serve the static asset.
}

require __DIR__ . '/index.php';
