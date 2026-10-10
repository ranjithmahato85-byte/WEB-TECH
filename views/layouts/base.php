<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title ?? 'Nepal Online Voting System - Secure Digital Voting') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/static/css/style.css">
  <link rel="stylesheet" href="/static/css/dashboard.css">
  <link rel="icon" type="image/svg+xml" href="/static/images/nepal-flag.svg">
  <?= $extra_head ?? '' ?>
</head>
<body>

  <!-- PUBLIC HEADER & NAVIGATION -->
  <header class="public-header">
    <div class="container header-inner">
      <a href="<?= url_for('index') ?>" class="brand-wrapper">
        <img src="/static/images/nepal-flag.svg" alt="Nepal Flag" class="brand-flag">
        <div class="brand-text">
          <span class="brand-title">Nepal Online Voting System</span>
          <span class="brand-subtitle">Your Vote, Our Future</span>
        </div>
      </a>

      <!-- Desktop Nav Links -->
      <nav>
        <ul class="nav-links">
          <li><a href="<?= url_for('index') ?>" class="nav-link <?= ($request_endpoint === 'index') ? 'active' : '' ?>">Home</a></li>
          <li><a href="<?= url_for('elections_list') ?>" class="nav-link <?= ($request_endpoint === 'elections_list') ? 'active' : '' ?>">Elections</a></li>
          <li><a href="<?= url_for('candidates_list') ?>" class="nav-link <?= (str_contains($request_path, 'candidates')) ? 'active' : '' ?>">Candidates</a></li>
          <li><a href="<?= url_for('results_list') ?>" class="nav-link <?= (str_contains($request_path, 'results')) ? 'active' : '' ?>">Results</a></li>
          <li><a href="<?= url_for('about') ?>" class="nav-link <?= ($request_endpoint === 'about') ? 'active' : '' ?>">About</a></li>
        </ul>
      </nav>

      <!-- Header Right Utilities -->
      <div class="header-right">
        <div class="lang-switch" title="Switch language / भाषा परिवर्तन गर्नुहोस्" role="button" tabindex="0">
          <span class="lang-opt active" data-lang="en">EN</span>
          <span class="lang-divider">|</span>
          <span class="lang-opt" data-lang="ne">नेपाली</span>
        </div>

        <div class="search-bar">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input type="text" placeholder="Search elections, candidates..." onkeydown="if(event.key==='Enter') window.location.href='<?= url_for('candidates_list') ?>?q='+encodeURIComponent(this.value)">
        </div>

        <div class="auth-actions">
          <?php if (!empty($current_user)): ?>
            <?php if ($current_user['role'] === 'admin'): ?>
              <a href="<?= url_for('admin_dashboard') ?>" class="btn btn-outline-primary btn-sm">Admin Panel</a>
            <?php else: ?>
              <a href="<?= url_for('voter_dashboard') ?>" class="btn btn-outline-primary btn-sm">Dashboard</a>
            <?php endif; ?>
            <a href="<?= url_for('logout') ?>" class="btn btn-outline btn-sm">Logout</a>
          <?php else: ?>
            <a href="<?= url_for('login') ?>" class="btn btn-outline-primary btn-sm">Login</a>
            <a href="<?= url_for('register') ?>" class="btn btn-primary btn-sm">Register</a>
          <?php endif; ?>
        </div>

        <button class="mobile-menu-btn" aria-label="Toggle Navigation">
          <svg width="24" height="24" viewBox="0 0 24 24" stroke="currentColor" fill="none" stroke-width="2">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>
  </header>

  <!-- FLASH MESSAGES -->
  <?php if (!empty($flashed_messages)): ?>
    <div class="flash-messages">
      <?php foreach ($flashed_messages as $flash): ?>
        <div class="alert alert-<?= ($flash['category'] === 'error') ? 'danger' : htmlspecialchars($flash['category']) ?>">
          <span><?= htmlspecialchars($flash['message']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- PAGE CONTENT -->
  <main>
    <?= $content ?? '' ?>
  </main>

  <!-- PUBLIC FOOTER (ONLY ON HOMEPAGE) -->
  <?php if ($request_endpoint === 'index'): ?>
  <footer class="public-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-brand">
          <div class="footer-logo">
            <img src="/static/images/nepal-flag.svg" alt="Nepal Flag" style="width: 26px; height: 32px;">
            <span class="footer-logo-text">Nepal Online Voting System</span>
          </div>
          <p class="footer-desc">
            A state-of-the-art secure digital balloting platform engineered to ensure transparent, accessible, and tamper-evident democratic voting for the Federal Democratic Republic of Nepal.
          </p>
          <div style="font-size: 0.8rem; color: #cbd5e1; margin-top: 8px;">
            <strong style="color: #ffffff;">Academic BCA Project Demo</strong> &bull; Non-partisan Simulation
          </div>
        </div>

        <div>
          <h4 class="footer-column-title">Quick Links</h4>
          <ul class="footer-links">
            <li><a href="<?= url_for('index') ?>" class="footer-link">Home</a></li>
            <li><a href="<?= url_for('elections_list') ?>" class="footer-link">Elections Directory</a></li>
            <li><a href="<?= url_for('candidates_list') ?>" class="footer-link">Candidates Directory</a></li>
            <li><a href="<?= url_for('results_list') ?>" class="footer-link">Election Results</a></li>
            <li><a href="<?= url_for('about') ?>" class="footer-link">Security Architecture</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-column-title">Voter Portal</h4>
          <ul class="footer-links">
            <li><a href="<?= url_for('login') ?>" class="footer-link">Voter Login</a></li>
            <li><a href="<?= url_for('register') ?>" class="footer-link">New Voter Registration</a></li>
            <li><a href="<?= url_for('voter_dashboard') ?>" class="footer-link">Voter Dashboard</a></li>
            <li><a href="<?= url_for('voting_status') ?>" class="footer-link">Check Voting Status</a></li>
            <li><a href="<?= url_for('admin_login') ?>" class="footer-link">Administrative Portal</a></li>
          </ul>
        </div>

        <div>
          <h4 class="footer-column-title">Security &amp; Trust</h4>
          <ul class="footer-links">
            <li class="footer-link" style="color: #cbd5e1;">Ballot Secrecy Protocol</li>
            <li class="footer-link" style="color: #cbd5e1;">End-to-End Audit Logs</li>
            <li class="footer-link" style="color: #cbd5e1;">Voter ID Verification</li>
            <li class="footer-link" style="color: #cbd5e1;">Election Commission Standards</li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom">
        <div>&copy; 2026 Nepal Online Voting System (Academic Project). All rights reserved.</div>
      </div>
    </div>
  </footer>
  <?php endif; ?>

  <script src="/static/js/main.js"></script>
  <script src="/static/js/i18n.js"></script>
  <?= $extra_scripts ?? '' ?>
</body>
</html>
