<?php
$page_title = "Voter Registration - Nepal Online Voting System";
?>
<div class="auth-page-wrapper" style="padding: 2.5rem 1rem;">
  <div style="width: 100%; max-width: 880px; background: #ffffff; border-radius: var(--radius-xl); border: 1px solid var(--border-color); box-shadow: var(--shadow-lg); overflow: hidden;">
    
    <!-- Top Header Banner -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 2rem 2.5rem; position: relative;">
      <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
          <span class="badge-pill-soft" style="background: rgba(255,255,255,0.1); color: #93c5fd;">Election Commission of Nepal</span>
          <h1 style="font-size: 1.75rem; font-weight: 800; margin-top: 6px;">Voter Registration</h1>
          <p style="color: #cbd5e1; font-size: 0.9rem;">Create your verified citizen account to participate in democratic online elections.</p>
        </div>
        <img src="/static/images/nepal-flag.svg" alt="Flag" style="width: 40px; height: 50px;">
      </div>
    </div>

    <!-- Registration Form Body -->
    <div style="padding: 2.5rem;">
      <form action="<?= url_for('register') ?>" method="POST">
        <?= csrf_field() ?>
        <!-- 2 Column Grid on Desktop -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;" class="form-row">
          
          <!-- Column 1: Personal & Contact Details -->
          <div>
            <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; border-bottom: 2px solid var(--primary-blue-light); padding-bottom: 6px;">
              1. Personal &amp; Contact Details
            </h3>

            <div class="form-group">
              <label class="form-label" for="full_name">Full Name <span class="required">*</span></label>
              <input type="text" id="full_name" name="full_name" class="form-control" placeholder="e.g. Ramesh Adhikari" required>
            </div>

            <div class="form-group">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <label class="form-label" for="dob" style="margin-bottom: 0;">Date of Birth <span class="required">*</span></label>
                <span class="badge badge-active" style="font-size: 0.72rem; padding: 2px 8px;">Age 18+ Required</span>
              </div>
              <input type="date" id="dob" name="dob" class="form-control" value="2000-01-15" max="<?= $max_eligible_dob ?>" required>
              <span style="font-size: 0.75rem; color: #64748b; margin-top: 3px; display: block;">
                Must be at least 18 years old to vote (born on or before <?= $max_eligible_dob ?>). Under 18 are not eligible.
              </span>
            </div>

            <div class="form-group">
              <label class="form-label" for="email">Email Address <span class="required">*</span></label>
              <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="mobile">Mobile Number <span class="required">*</span></label>
              <div style="display: flex; gap: 8px;">
                <span style="display: inline-flex; align-items: center; padding: 0 10px; background: #f1f5f9; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-weight: 600; font-size: 0.88rem; color: #475569;">+977</span>
                <input type="tel" id="mobile" name="mobile" class="form-control" placeholder="98XXXXXXXX" pattern="[0-9]{10}" required>
              </div>
            </div>
          </div>

          <!-- Column 2: Electoral Constituency & Credentials -->
          <div>
            <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem; border-bottom: 2px solid var(--primary-blue-light); padding-bottom: 6px;">
              2. Electoral Constituency &amp; Location
            </h3>

            <div class="form-group">
              <label class="form-label" for="province">Province <span class="required">*</span></label>
              <select id="province" name="province" class="form-control" required>
                <option value="">Select Province</option>
                <option value="Koshi">Koshi Province</option>
                <option value="Madhesh">Madhesh Province</option>
                <option value="Bagmati" selected>Bagmati Province</option>
                <option value="Gandaki">Gandaki Province</option>
                <option value="Lumbini">Lumbini Province</option>
                <option value="Karnali">Karnali Province</option>
                <option value="Sudurpashchim">Sudurpashchim Province</option>
              </select>
            </div>

            <div style="display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 10px;">
              <div class="form-group">
                <label class="form-label" for="district">District <span class="required">*</span></label>
                <input type="text" id="district" name="district" class="form-control" value="Kathmandu" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="ward">Ward No. <span class="required">*</span></label>
                <input type="number" id="ward" name="ward" class="form-control" value="10" min="1" max="35" required>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="municipality">Municipality / Rural Municipality <span class="required">*</span></label>
              <input type="text" id="municipality" name="municipality" class="form-control" value="Kathmandu Metropolitan City" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
              <div class="form-group">
                <label class="form-label" for="voter_id">Voter ID Card No. <span class="required">*</span></label>
                <input type="text" id="voter_id" name="voter_id" class="form-control" placeholder="e.g. NV-2026-9912" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="citizenship_no">Citizenship Identity (नागरिकता नं.) <span class="required">*</span></label>
                <input type="text" id="citizenship_no" name="citizenship_no" class="form-control" placeholder="e.g. 27-01-76-08891" required>
              </div>
            </div>
          </div>
        </div>

        <!-- Account Credentials Row -->
        <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
          <h3 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin-bottom: 1rem;">
            3. Account Login Credentials
          </h3>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;" class="form-row">
            <div class="form-group">
              <label class="form-label" for="reg_username">Username <span class="required">*</span></label>
              <input type="text" id="reg_username" name="username" class="form-control" placeholder="choose username" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="reg_password">Password <span class="required">*</span></label>
              <input type="password" id="reg_password" name="password" class="form-control" placeholder="minimum 6 characters" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="confirm_password">Confirm Password <span class="required">*</span></label>
              <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="re-enter password" required>
            </div>
          </div>

          <div style="margin: 1.25rem 0;">
            <label class="form-check">
              <input type="checkbox" name="terms" required checked>
              <span>I confirm that the details provided are true and I agree to the <a href="<?= url_for('about') ?>" target="_blank">Electoral Terms &amp; Conditions</a>.</span>
            </label>
          </div>

          <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="font-size: 0.9rem; color: var(--text-muted);">
              Already registered? <a href="<?= url_for('login') ?>" style="font-weight: 600;">Go to Login</a>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg" style="min-width: 200px;">
              Register Voter
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
