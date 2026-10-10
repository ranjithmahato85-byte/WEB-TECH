<?php
/**
 * Nepal Online Voting System
 * View Rendering & Layout Engine
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';

function render_view(string $viewName, array $data = [], string $layout = 'base'): void {
    // Inject global view data
    $currentUser = current_user();
    $today = new DateTime();
    $today->modify('-18 years');
    $maxEligibleDob = $today->format('Y-m-d');

    $globalData = [
        'current_user' => $currentUser,
        'now_year' => (int)date('Y'),
        'max_eligible_dob' => $maxEligibleDob,
        'request_endpoint' => $GLOBALS['CURRENT_ROUTE'] ?? '',
        'request_path' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
        'flashed_messages' => get_flashed_messages(),
    ];

    $merged = array_merge($globalData, $data);
    extract($merged, EXTR_SKIP);

    // Capture child view content
    $viewPath = __DIR__ . '/../views/' . ltrim($viewName, '/');
    if (!str_ends_with($viewPath, '.php')) {
        $viewPath .= '.php';
    }

    if (!file_exists($viewPath)) {
        http_response_code(500);
        echo "<h1>View Error</h1><p>View file not found: " . htmlspecialchars($viewName) . "</p>";
        exit;
    }

    ob_start();
    include $viewPath;
    $content = ob_get_clean();

    // If no layout requested, render directly
    if ($layout === 'none' || empty($layout)) {
        echo $content;
        return;
    }

    // Wrap with layout
    $layoutPath = __DIR__ . '/../views/layouts/' . $layout . '.php';
    if (!file_exists($layoutPath)) {
        echo $content;
        return;
    }

    include $layoutPath;
}
