<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Election Administration - Nepal Online Voting System') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/static/css/style.css">
  <link rel="stylesheet" href="/static/css/dashboard.css">
  <link rel="icon" type="image/svg+xml" href="/static/images/nepal-flag.svg">
  <?= $extra_head ?? '' ?>
</head>
<body>

  <!-- FLASH MESSAGES (auto-dismissing toasts) -->
  <?php if (!empty($flashed_messages)): ?>
    <div class="flash-messages">
      <?php foreach ($flashed_messages as $flash): ?>
        <div class="alert alert-<?= ($flash['category'] === 'error') ? 'danger' : htmlspecialchars($flash['category']) ?>">
          <span><?= htmlspecialchars($flash['message']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

<div class="dashboard-wrapper">
  <!-- ADMIN DARK NAVY SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-user" style="border-bottom-color: #334155;">
      <div class="sidebar-avatar" style="border-color: #ef4444; background: #1e293b;">
        <svg fill="none" stroke="#f87171" viewBox="0 0 24 24" stroke-width="2" style="width: 26px; height: 26px; margin: 8px auto;">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
      </div>
      <div class="sidebar-user-info">
        <span class="sidebar-user-name">EC Admin</span>
        <div class="sidebar-user-role">
          <span style="color: #f87171;">Election Commissioner</span>
        </div>
      </div>
    </div>

    <!-- Admin Nav -->
    <nav class="sidebar-nav">
      <a href="<?= url_for('admin_dashboard') ?>" class="sidebar-link <?= ($request_endpoint === 'admin_dashboard') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
        </svg>
        <span>Admin Dashboard</span>
      </a>

      <a href="<?= url_for('admin_elections') ?>" class="sidebar-link <?= ($request_endpoint === 'admin_elections') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <span>Elections</span>
      </a>

      <a href="<?= url_for('admin_candidates') ?>" class="sidebar-link <?= ($request_endpoint === 'admin_candidates') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <span>Candidates</span>
      </a>

      <a href="<?= url_for('admin_voters') ?>" class="sidebar-link <?= ($request_endpoint === 'admin_voters') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        <span>Voters</span>
      </a>

      <a href="<?= url_for('admin_results') ?>" class="sidebar-link <?= ($request_endpoint === 'admin_results') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <span>Results Audit</span>
      </a>

      <a href="<?= url_for('admin_reports') ?>" class="sidebar-link <?= ($request_endpoint === 'admin_reports') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>Reports & Logs</span>
      </a>

      <a href="<?= url_for('logout') ?>" class="sidebar-link" style="margin-top: auto; color: #f87171;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        <span>Logout Admin</span>
      </a>
    </nav>

    <div class="sidebar-footer">
      <img src="/static/images/nepal-flag.svg" alt="Nepal Flag" class="sidebar-flag">
      <span class="sidebar-motto">Election Commission of Nepal &bull; Academic Portal</span>
    </div>
  </aside>

  <!-- MAIN ADMIN CONTENT -->
  <div class="main-content">
    <header class="top-bar">
      <div class="page-headline">
        <h1 class="page-title"><?= htmlspecialchars($section_title ?? 'System Administration') ?></h1>
        <span class="page-subtext"><?= htmlspecialchars($section_subtitle ?? 'Central Control & Election Auditing') ?></span>
      </div>

      <div class="top-bar-actions">
        <a href="<?= url_for('index') ?>" class="btn btn-outline btn-sm">Public Portal</a>
      </div>
    </header>

    <div class="content-body">
      <?= $content ?? '' ?>
    </div>
  </div>
</div>

<script src="/static/js/main.js"></script>
<?= $extra_scripts ?? '' ?>
</body>
</html>
