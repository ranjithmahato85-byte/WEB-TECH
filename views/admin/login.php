<?php
$page_title = "Administrator Login - Election Commission Portal";
?>
<div class="auth-page-wrapper">
  <div class="auth-split-card" style="max-width: 840px;">
    <!-- LEFT SIDE: ADMIN SECURITY THEME -->
    <div class="auth-left-banner" style="background: linear-gradient(135deg, #1e1b4b 0%, #0f172a 100%);">
      <div class="auth-left-content">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(239,68,68,0.2); border: 1px solid #ef4444; padding: 4px 12px; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; color: #fca5a5;">
          <span>Restricted Admin Portal</span>
        </div>

        <h2 class="auth-left-title" style="margin-top: 1.25rem;">Election Administration System</h2>
        <p class="auth-left-desc">
          Official management console for authorized Election Commission officers to oversee elections, candidate nominations, voter registrations, and cryptographically verified election tallies.
        </p>
      </div>

      <div style="position: relative; z-index: 2; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 1rem; font-size: 0.8rem; color: #94a3b8;">
        <strong>Academic Viva Admin Login:</strong><br>
        Use an authorized administrator account.
      </div>
    </div>

    <!-- RIGHT SIDE: ADMIN LOGIN FORM -->
    <div class="auth-form-side">
      <div class="auth-header">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
          <span class="badge" style="background: #fee2e2; color: #b91c1c; font-size: 0.72rem;">ADMIN AUTHENTICATION</span>
        </div>
        <h2 class="auth-title">Administrative Access</h2>
        <p class="auth-subtitle">Enter your official commission credentials to proceed.</p>
      </div>

      <form action="<?= url_for('admin_login') ?>" method="POST">
        <?= csrf_field() ?>
        <div class="form-group">
          <label class="form-label" for="admin_username">Admin Username</label>
          <div class="input-with-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <input type="text" id="admin_username" name="username" class="form-control" placeholder="Admin username" required autofocus>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="admin_password">Password</label>
          <div class="input-with-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <input type="password" id="admin_password" name="password" class="form-control" placeholder="Password" required>
          </div>
        </div>

        <button type="submit" class="btn btn-crimson" style="width: 100%; padding: 11px; margin-top: 10px;">
          Authenticate as Administrator
        </button>

        <div style="text-align: center; margin-top: 1.25rem;">
          <a href="<?= url_for('login') ?>" style="font-size: 0.85rem; color: var(--text-muted);">
            &larr; Switch to Citizen / Voter Login
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
