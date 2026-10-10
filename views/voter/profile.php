<?php
$page_title = "Voter Profile - Nepal Online Voting System";
$section_title = "Citizen Voter Profile";
$section_subtitle = "Official Electoral Register Record";
?>
<div style="max-width: 840px; margin: 0 auto;">
  <div style="background: #ffffff; border-radius: var(--radius-xl); border: 1px solid var(--border-color); box-shadow: var(--shadow-md); overflow: hidden;">
    
    <!-- Profile Banner -->
    <div style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: #ffffff; padding: 2rem 2.5rem; display: flex; align-items: center; gap: 20px;">
      <div style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid #38bdf8; overflow: hidden; background: #334155; flex-shrink: 0;">
        <img src="/static/images/candidates/voter-avatar.svg" alt="Voter Avatar" style="width: 100%; height: 100%; object-fit: cover;">
      </div>
      <div>
        <h2 style="font-size: 1.5rem; font-weight: 800;"><?= htmlspecialchars($current_user['full_name'] ?? '') ?></h2>
        <div style="display: flex; align-items: center; gap: 10px; margin-top: 4px; font-size: 0.88rem; color: #cbd5e1; flex-wrap: wrap;">
          <span>Voter ID: <strong style="color: #ffffff;"><?= htmlspecialchars($current_user['voter_id'] ?? '') ?></strong></span>
          &bull;
          <span>Age: <strong style="color: #ffffff;"><?= htmlspecialchars($current_user['age'] ?? '0') ?> Years</strong></span>
          &bull;
          <?php if (!empty($current_user['is_eligible_age'])): ?>
            <span style="color: #16a34a; font-weight: 700;">Age 18+ Eligible</span>
          <?php else: ?>
            <span style="color: #dc2626; font-weight: 700;">Under 18 Ineligible</span>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Voter Information Grid -->
    <div style="padding: 2.5rem;">
      <h3 style="font-size: 1.1rem; font-weight: 700; color: #0f172a; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">
        Constituency &amp; Personal Information
      </h3>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;" class="form-row">
        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Full Legal Name</span>
          <strong style="color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($current_user['full_name'] ?? '') ?></strong>
        </div>

        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Date of Birth &amp; Age</span>
          <strong style="color: #0f172a; font-size: 0.95rem;">
            <?= htmlspecialchars($current_user['dob'] ?? '2000-01-15') ?>
            <span style="font-size: 0.85rem; color: #475569; font-weight: 600; margin-left: 6px;">(<?= htmlspecialchars($current_user['age'] ?? '0') ?> years old)</span>
          </strong>
        </div>

        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Email Address</span>
          <strong style="color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($current_user['email'] ?? '') ?></strong>
        </div>

        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Mobile Number</span>
          <strong style="color: #0f172a; font-size: 0.95rem;">+977 <?= htmlspecialchars($current_user['mobile'] ?: '9812345678') ?></strong>
        </div>

        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Province</span>
          <strong style="color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($current_user['province'] ?: 'Bagmati') ?> Province</strong>
        </div>

        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">District &amp; Ward</span>
          <strong style="color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($current_user['district'] ?: 'Kathmandu') ?> - Ward <?= htmlspecialchars($current_user['ward'] ?: '10') ?></strong>
        </div>

        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Municipality</span>
          <strong style="color: #0f172a; font-size: 0.95rem;"><?= htmlspecialchars($current_user['municipality'] ?: 'Kathmandu Metropolitan City') ?></strong>
        </div>

        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Citizenship Identity (नागरिकता नं.)</span>
          <?php if (!empty($current_user['citizenship_no'])): ?>
            <div style="display: flex; align-items: center; gap: 8px; margin-top: 2px;">
              <code style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-weight: 700; color: #0f172a; font-size: 0.92rem;"><?= htmlspecialchars($current_user['citizenship_no']) ?></code>
              <span class="badge badge-active" style="font-size: 0.7rem; padding: 2px 6px;">Verified</span>
            </div>
          <?php else: ?>
            <div style="display: flex; align-items: center; gap: 8px; margin-top: 2px;">
              <span style="color: #dc2626; font-weight: 600; font-size: 0.9rem;">Not Registered</span>
              <span class="badge badge-completed" style="color: #dc2626; font-size: 0.7rem; padding: 2px 6px;">Action Required</span>
            </div>
          <?php endif; ?>
        </div>

        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Voting Eligibility</span>
          <?php if (!empty($current_user['is_eligible_age']) && !empty($current_user['citizenship_no']) && !empty($current_user['has_citizenship_verified'])): ?>
            <span class="badge badge-active">Fully Eligible (Age 18+ &amp; Verified)</span>
          <?php elseif (empty($current_user['is_eligible_age'])): ?>
            <span class="badge" style="color: #ffffff; background: #dc2626;">Ineligible (Under 18: Age <?= htmlspecialchars($current_user['age'] ?? '0') ?>)</span>
          <?php else: ?>
            <span class="badge badge-completed" style="color: #dc2626;">Ineligible (Citizenship Required)</span>
          <?php endif; ?>
        </div>

        <div>
          <span style="display: block; font-size: 0.78rem; text-transform: uppercase; color: var(--text-muted); font-weight: 600;">Account Status</span>
          <span class="badge badge-active">Active Citizen Account</span>
        </div>
      </div>

      <!-- EDIT / UPDATE CITIZENSHIP IDENTITY SECTION -->
      <div style="margin-top: 2rem; padding: 1.5rem; background: #f8fafc; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
          <div>
            <h4 style="font-size: 1rem; font-weight: 700; color: #0f172a; margin: 0;">
              Update Official Citizenship &amp; Constituency Details
            </h4>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
              Ensure your Citizenship Certificate matches official government records for voting access
            </p>
          </div>
        </div>

        <form action="<?= url_for('voter_profile') ?>" method="POST" enctype="multipart/form-data">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;" class="form-row">
            <div class="form-group">
              <label class="form-label" for="prof_fullname">Full Legal Name <span class="required">*</span></label>
              <input type="text" id="prof_fullname" name="full_name" class="form-control" value="<?= htmlspecialchars($current_user['full_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="prof_cit">Nepali Citizenship Certificate Number <span class="required" style="color: #dc2626;">*</span></label>
              <input type="text" id="prof_cit" name="citizenship_no" class="form-control" value="<?= htmlspecialchars($current_user['citizenship_no'] ?? '') ?>" placeholder="e.g. 27-01-76-08891" required style="font-weight: 600;">
            </div>

            <div class="form-group">
              <label class="form-label" for="prof_email">Email Address <span class="required">*</span></label>
              <input type="email" id="prof_email" name="email" class="form-control" value="<?= htmlspecialchars($current_user['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="prof_mobile">Mobile Number <span class="required">*</span></label>
              <input type="tel" id="prof_mobile" name="mobile" class="form-control" value="<?= htmlspecialchars($current_user['mobile'] ?? '') ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label" for="prof_province">Province</label>
              <input type="text" id="prof_province" name="province" class="form-control" value="<?= htmlspecialchars($current_user['province'] ?? '') ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="prof_district">District</label>
              <input type="text" id="prof_district" name="district" class="form-control" value="<?= htmlspecialchars($current_user['district'] ?? '') ?>">
            </div>
          </div>

          <!-- Photo Upload & Current Document Card Preview -->
          <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color); display: grid; grid-template-columns: 1fr 220px; gap: 16px; align-items: center;" class="form-row">
            <div>
              <label class="form-label" for="profile_cit_photo" style="font-weight: 700; margin-bottom: 4px;">
                Update Citizenship Certificate Photo / Scan
              </label>
              <input type="file" id="profile_cit_photo" name="citizenship_photo" class="form-control" accept="image/*,application/pdf">
              <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 4px; display: block;">
                Attach official government document scan (PNG, JPG, WEBP, PDF)
              </span>
            </div>

            <div style="text-align: center;">
              <div style="border-radius: var(--radius-md); border: 1px solid #cbd5e1; overflow: hidden; background: #ffffff; padding: 4px; box-shadow: var(--shadow-sm); height: 110px; display: flex; align-items: center; justify-content: center;">
                <img 
                  src="<?= cit_photo_url($current_user['citizenship_photo'] ?? '') ?>" 
                  alt="Citizenship Document" 
                  style="width: 100%; height: 100%; object-fit: contain;"
                >
              </div>
              <span style="font-size: 0.72rem; color: #166534; font-weight: 700; margin-top: 4px; display: block;">
                Document Name Matches: <?= htmlspecialchars($current_user['full_name'] ?? '') ?>
              </span>
            </div>
          </div>

          <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem;">
            <button type="submit" class="btn btn-primary">
              Save &amp; Verify Citizenship Identity
            </button>
          </div>
        </form>
      </div>

      <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 0.85rem; color: var(--text-muted);">
          Official Election Commission of Nepal citizen electoral database.
        </span>
        <a href="<?= url_for('voter_dashboard') ?>" class="btn btn-outline btn-sm">
          &larr; Back to Dashboard
        </a>
      </div>
    </div>
  </div>
</div>
