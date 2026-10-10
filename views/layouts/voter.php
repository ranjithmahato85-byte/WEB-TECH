<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Voter Dashboard - Nepal Online Voting System') ?></title>
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
  <!-- DARK NAVY SIDEBAR -->
  <aside class="sidebar">
    <!-- User Profile Badge -->
    <div class="sidebar-user">
      <div class="sidebar-avatar">
        <img src="/static/images/candidates/voter-avatar.svg" alt="Voter Avatar">
      </div>
      <div class="sidebar-user-info">
        <span class="sidebar-user-name"><?= htmlspecialchars($current_user['full_name'] ?? 'Ranjeet Kumar') ?></span>
        <div class="sidebar-user-role">
          <span>Verified Voter</span>
        </div>
      </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-nav">
      <a href="<?= url_for('voter_dashboard') ?>" class="sidebar-link <?= ($request_endpoint === 'voter_dashboard') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>Dashboard</span>
      </a>

      <a href="<?= url_for('my_elections') ?>" class="sidebar-link <?= ($request_endpoint === 'my_elections') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span>My Elections</span>
        <span class="sidebar-badge"><?= $active_elections_count ?? '3' ?></span>
      </a>

      <a href="<?= url_for('candidates_list') ?>" class="sidebar-link <?= ($request_endpoint === 'candidates_list') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <span>Candidates</span>
      </a>

      <a href="<?= url_for('voting_status') ?>" class="sidebar-link <?= ($request_endpoint === 'voting_status') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Voting Status</span>
      </a>

      <a href="<?= url_for('voter_profile') ?>" class="sidebar-link <?= ($request_endpoint === 'voter_profile') ? 'active' : '' ?>">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <span>Profile</span>
      </a>

      <a href="<?= url_for('logout') ?>" class="sidebar-link" style="margin-top: auto; color: #f87171;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
        </svg>
        <span>Logout</span>
      </a>
    </nav>

    <!-- Sidebar Footer Motif -->
    <div class="sidebar-footer">
      <img src="/static/images/nepal-flag.svg" alt="Nepal Flag" class="sidebar-flag">
      <span class="sidebar-motto">Together for a Stronger Democracy in Nepal</span>
    </div>
  </aside>

  <!-- MAIN CONTENT AREA -->
  <div class="main-content">
    <header class="top-bar">
      <div class="page-headline">
        <h1 class="page-title"><?= htmlspecialchars($section_title ?? 'Voter Portal') ?></h1>
        <span class="page-subtext"><?= htmlspecialchars($section_subtitle ?? 'National Democratic Online Voting System') ?></span>
      </div>

      <div class="top-bar-actions">
        <a href="<?= url_for('index') ?>" class="btn btn-outline btn-sm">Public Site</a>
        <div class="lang-switch" title="Switch language / भाषा परिवर्तन गर्नुहोस्" role="button" tabindex="0">
          <span class="lang-opt active" data-lang="en">EN</span>
          <span class="lang-divider">|</span>
          <span class="lang-opt" data-lang="ne">नेपाली</span>
        </div>
      </div>
    </header>

    <div class="content-body">
      <?= $content ?? '' ?>
    </div>
  </div>
</div>

<script src="/static/js/main.js"></script>
<script src="/static/js/i18n.js"></script>
<?= $extra_scripts ?? '' ?>
</body>
</html>
