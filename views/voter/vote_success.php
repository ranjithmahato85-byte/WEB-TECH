<?php
$page_title = "Vote Recorded - Nepal Online Voting System";
$section_title = "Ballot Submitted";
$section_subtitle = "Official Electoral Participation Receipt";
?>
<div class="success-container">
  
  <!-- GREEN CIRCLE CHECKMARK WITH CONFETTI -->
  <div class="success-icon-wrap">
    <span class="confetti-dot c-1"></span>
    <span class="confetti-dot c-2"></span>
    <span class="confetti-dot c-3"></span>
    <span class="confetti-dot c-4"></span>
    <span class="confetti-dot c-5"></span>

    <div class="success-circle">
      <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
      </svg>
    </div>
  </div>

  <h2 class="success-title">Your Vote Has Been Successfully Recorded!</h2>
  <p class="success-desc">Thank you for participating in the democratic election.</p>

  <!-- MINT GREEN CONFIRMATION BOX -->
  <div class="confirmation-box">
    <div class="conf-label">Confirmation Number</div>
    <div class="conf-number"><?= htmlspecialchars($confirmation_code ?: 'NV20260901084523') ?></div>
    <div class="conf-note">Please save this confirmation number for your records.</div>
  </div>

  <!-- BALLOT SECRECY ASSURANCE -->
  <div class="secrecy-badge">
    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
    <span>Ballot Secrecy Maintained: Candidate choice is encrypted and unlinked from your ID</span>
  </div>

  <!-- ELECTION & TIMESTAMP METADATA -->
  <div style="background: #f8fafc; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 2rem; text-align: left; font-size: 0.88rem;">
    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
      <span style="color: var(--text-muted);">Election:</span>
      <strong style="color: #0f172a;"><?= htmlspecialchars($election_title ?? 'Federal Parliamentary Election 2026') ?></strong>
    </div>
    <div style="display: flex; justify-content: space-between;">
      <span style="color: var(--text-muted);">Submission Timestamp:</span>
      <span style="color: #0f172a; font-family: monospace;"><?= htmlspecialchars($vote_time ?? date('F d, Y at h:i A')) ?></span>
    </div>
  </div>

  <!-- ACTION BUTTONS -->
  <div class="success-actions">
    <?php if (!empty($election_id)): ?>
    <a href="<?= url_for('election_detail', ['election_id' => $election_id]) ?>" class="btn btn-primary btn-lg">
      View Election Details
    </a>
    <?php endif; ?>

    <a href="<?= url_for('voter_dashboard') ?>" class="btn btn-outline btn-lg">
      Go to Dashboard
    </a>
  </div>
</div>
