<?php
$page_title = "Voter Login - Nepal Online Voting System";
?>
<div class="auth-page-wrapper">
  <div class="auth-split-card">
    <!-- LEFT SIDE: NEPAL THEME & MESSAGE -->
    <div class="auth-left-banner">
      <div class="auth-left-content">
        <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.1); padding: 4px 12px; border-radius: 9999px; font-size: 0.8rem; font-weight: 600;">
          <img src="/static/images/nepal-flag.svg" alt="Flag" style="width: 16px; height: 20px;">
          <span>Citizen Portal</span>
        </div>

        <h2 class="auth-left-title">Empowering Every Nepali Citizen</h2>
        <p class="auth-left-desc">
          Your vote is your voice in shaping the future of Nepal. Access your verified ballot safely and exercise your democratic right with complete secrecy.
        </p>
      </div>

      <div style="position: relative; z-index: 2; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 1.25rem;">
        <div style="font-size: 0.8rem; color: #94a3b8;">
          <strong style="color: #ffffff;">Academic Viva Demo Accounts:</strong><br>
          &bull; <strong>Voter:</strong> <code>ranjeet</code> &bull; Pass: <code>password123</code><br>
          &bull; <strong>Admin:</strong> <code>admin</code> &bull; Pass: <code>admin123</code>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDE: DEDICATED LOGIN FORM -->
    <div class="auth-form-side">
      <div class="auth-header">
        <h2 class="auth-title">Login to Your Account</h2>
        <p class="auth-subtitle">Access your account to participate in national elections.</p>
      </div>

      <form action="<?= url_for('login') ?>" method="POST">
        <div class="form-group">
          <label class="form-label" for="username">Username or Email</label>
          <div class="input-with-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username or email" required autofocus>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-with-icon">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
          </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
          <label class="form-check">
            <input type="checkbox" name="remember_me" checked>
            <span>Remember me</span>
          </label>
          <a href="#" onclick="alert('For academic demo: please use the demo credentials provided on the left.'); return false;" style="font-size: 0.85rem;">
            Forgot password?
          </a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px;">
          <button type="submit" class="btn btn-primary" style="width: 100%; padding: 11px;">
            Login
          </button>
          
          <div style="text-align: center; margin-top: 10px; font-size: 0.88rem; color: var(--text-muted);">
            Don't have an account? <a href="<?= url_for('register') ?>" style="font-weight: 600;">Register</a>
          </div>

          <!-- Viva Quick Test Buttons -->
          <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--border-color); display: flex; gap: 8px;">
            <button type="button" class="btn btn-outline btn-sm" style="flex: 1;" onclick="fillVoter()">
              Fill Demo Voter
            </button>
            <button type="button" class="btn btn-outline btn-sm" style="flex: 1;" onclick="fillAdmin()">
              Fill Demo Admin
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function fillVoter() {
  document.getElementById('username').value = 'ranjeet';
  document.getElementById('password').value = 'password123';
}
function fillAdmin() {
  document.getElementById('username').value = 'admin';
  document.getElementById('password').value = 'admin123';
}
</script>
